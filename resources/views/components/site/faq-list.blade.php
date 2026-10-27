@props([
    'faqs',
    'id' => 'daftar-faq',
    'tone' => 'light',
])

@php
    $dark = $tone !== 'light';
@endphp

<div
    {{ $attributes->class([
        'faq-thread-list',
        'faq-thread-list--dark' => $dark,
    ]) }}
    id="{{ $id }}"
    data-faq-thread
>
    @foreach ($faqs as $index => $faq)
        @php
            $isOpen = $index === 0;
            $questionId = $id.'-question-'.$index;
            $answerId = $id.'-answer-'.$index;
        @endphp

        <article
            @class(['faq-thread-item', 'reveal', 'is-open' => $isOpen])
            style="--reveal-delay: {{ min($index, 4) * 70 }}ms"
            data-faq-item
        >
            <h3>
                <button
                    id="{{ $questionId }}"
                    type="button"
                class="faq-thread-question"
                    data-faq-trigger
                    aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                    aria-controls="{{ $answerId }}"
                >
                    <span class="faq-thread-question-bubble">
                        <span class="faq-thread-question-number" aria-hidden="true">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="faq-thread-question-text">{{ $faq->question }}</span>
                    </span>

                    <span
                        class="faq-thread-toggle"
                        aria-hidden="true"
                    >
                        <x-site.icon name="plus" class="size-5" stroke="1.6" />
                    </span>
                </button>
            </h3>

            <div
                id="{{ $answerId }}"
                class="faq-thread-answer"
                data-faq-panel
                role="region"
                aria-labelledby="{{ $questionId }}"
                @if (! $isOpen) hidden @else style="height: auto" @endif
            >
                <div class="faq-thread-answer-inner">
                    <span class="faq-thread-avatar" aria-hidden="true">
                        <img src="/images/logo-navbar.png" alt="" />
                    </span>
                    <div class="faq-thread-answer-bubble">
                        <p>{{ $faq->answer }}</p>
                    </div>
                </div>
            </div>
        </article>
    @endforeach
</div>
