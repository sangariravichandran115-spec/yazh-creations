<?php

namespace App\View\Components;

use App\Services\WhatsAppService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class WhatsAppButton extends Component
{
    /**
     * Pre-built deep link to use instead of the default brand message.
     */
    public ?string $link;

    /**
     * Pre-filled message text (used when no explicit link is given).
     */
    public ?string $message;

    /**
     * Render as a fixed floating action button.
     */
    public bool $floating = false;

    public function __construct(
        protected WhatsAppService $whatsapp,
        ?string $link = null,
        ?string $message = null,
        bool $floating = false,
    ) {
        $this->link = $link;
        $this->message = $message;
        $this->floating = $floating;
    }

    /**
     * Resolve the link, falling back to a generic brand message.
     */
    public function resolvedLink(): string
    {
        if ($this->link) {
            return $this->link;
        }

        return $this->whatsapp->link($this->whatsapp->buildBrandMessage($this->message));
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.whatsapp-button');
    }
}
