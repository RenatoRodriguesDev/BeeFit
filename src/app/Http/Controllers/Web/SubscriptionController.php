<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Webhook;
use Stripe\Event;
use App\Models\User;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    // ─── Página de planos ──────────────────────────────────────────

    public function plans()
    {
        return view('subscription.plans', [
            'user' => Auth::user(),
        ]);
    }

    // ─── Iniciar checkout Stripe ───────────────────────────────────

    public function checkout(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:premium,trainer',
        ]);

        $user    = Auth::user();
        $priceId = config('services.stripe.prices.' . $request->plan);

        $session = StripeSession::create([
            'customer_email'       => $user->email,
            'mode'                 => 'subscription',
            'payment_method_types' => ['card'],
            'line_items'           => [[
                'price'    => $priceId,
                'quantity' => 1,
            ]],
            'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('subscription.plans'),
            'metadata'    => [
                'user_id' => $user->id,
                'plan'    => $request->plan,
            ],
        ]);

        return redirect($session->url);
    }

    // ─── Sucesso após pagamento ────────────────────────────────────

    public function success(Request $request)
    {
        $session = StripeSession::retrieve($request->session_id);

        // Verify this session belongs to the authenticated user
        if ((int) $session->metadata->user_id !== Auth::id()) {
            abort(403);
        }

        if ($session->payment_status === 'paid') {
            $user = Auth::user();
            $user->stripe_customer_id     = $session->customer;
            $user->stripe_subscription_id = $session->subscription;
            $user->subscription_status    = 'active';
            $user->plan                   = $session->metadata->plan;
            $user->role                   = $session->metadata->plan;
            $user->save();
        }

        return redirect()->route('dashboard')
            ->with('success', __('app.subscription_activated'));
    }

    // ─── Portal Stripe (gerir subscrição) ─────────────────────────

    public function portal()
    {
        $user    = Auth::user();
        $session = \Stripe\BillingPortal\Session::create([
            'customer'   => $user->stripe_customer_id,
            'return_url' => route('profile.edit'),
        ]);

        return redirect($session->url);
    }

    // ─── Webhook Stripe ────────────────────────────────────────────

    public function webhook(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            return response('Webhook error: ' . $e->getMessage(), 400);
        }

        match ($event->type) {
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($event),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event),
            'invoice.payment_failed'        => $this->handlePaymentFailed($event),
            default                         => null,
        };

        return response('OK', 200);
    }

    private function handleSubscriptionUpdated(Event $event): void
    {
        $sub  = $event->data->object;
        $user = User::where('stripe_customer_id', $sub->customer)->first();
        if (!$user) return;

        $user->subscription_status  = $sub->status;
        $user->subscription_ends_at = $sub->current_period_end
            ? \Carbon\Carbon::createFromTimestamp($sub->current_period_end)
            : null;
        $user->save();
    }

    private function handleSubscriptionDeleted(Event $event): void
    {
        $sub  = $event->data->object;
        $user = User::where('stripe_customer_id', $sub->customer)->first();
        if (!$user) return;

        $user->plan                = 'free';
        $user->role                = 'user';
        $user->subscription_status = 'canceled';
        $user->save();
    }

    private function handlePaymentFailed(Event $event): void
    {
        $invoice = $event->data->object;
        $user    = User::where('stripe_customer_id', $invoice->customer)->first();
        if (!$user) return;

        $user->subscription_status = 'past_due';
        $user->save();
    }
}