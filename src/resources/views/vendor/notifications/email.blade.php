<x-mail::message>

{{-- HEADER --}}
<div style="text-align:center; margin-bottom:20px;">
<h1 style="font-weight:700; font-size:28px;">
🏋️‍♂️ BeeFit
</h1>

<p style="opacity:0.7;">
{{ __('mail.fitness_slogan') ?? 'Train. Progress. Improve.' }}
</p>
</div>

---

# 🔑 {{ __('mail.hello') }}

<p style="line-height:1.6;">
{{ __('mail.gym_intro') ?? 'Está a receber este email porque foi solicitada uma redefinição da sua palavra-passe.' }}
</p>

<x-mail::panel>
🔥 {{ __('mail.gym_reset_hint') ?? 'Clique no botão abaixo para definir uma nova palavra-passe e voltar ao treino.' }}
</x-mail::panel>

<div style="text-align:center; margin:30px 0;">
<x-mail::button :url="$actionUrl" color="primary">
🏋️‍♂️ {{ $actionText }}
</x-mail::button>
</div>

<p style="text-align:center; font-size:14px; opacity:0.8;">
{{ __('mail.gym_no_action') ?? 'Se não solicitou esta operação, pode ignorar este email.' }}
</p>

---

## 💡 {{ __('mail.gym_tip_title') ?? 'Dica Fitness' }}

<p style="font-style:italic; opacity:0.85;">
{{ __('mail.gym_tip') ?? 'A consistência é mais importante que a intensidade. Treine todos os dias, mesmo que seja pouco.' }}
</p>

---

@if(isset($displayableActionUrl))
<x-slot:subcopy>
{{ __('mail.trouble_clicking', ['actionText' => $actionText]) }}

<span class="break-all">
[{{ $displayableActionUrl }}]({{ $actionUrl }})
</span>
</x-slot:subcopy>
@endif

---

<div style="text-align:right;">
{{ __('mail.regards') }},<br>
<strong>BeeFit 💪</strong>
</div>

</x-mail::message>