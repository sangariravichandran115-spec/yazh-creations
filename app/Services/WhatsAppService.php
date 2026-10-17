<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;

class WhatsAppService
{
    /**
     * Build the message body sent to the business for a product enquiry.
     */
    public function buildMessage(Product $product, ?string $size = null, ?string $name = null, ?string $phone = null, ?string $message = null): string
    {
        $lines = [
            'Hi Yazh Creations,',
            '',
            "I'm interested in:",
            '',
            "Product: {$product->name}",
            'Price: ₹'.number_format((float) $product->sellingPrice(), 0),
        ];

        if ($size) {
            $lines[] = "Size: {$size}";
        }

        if ($name) {
            $lines[] = "Name: {$name}";
        }

        if ($phone) {
            $lines[] = "Phone: {$phone}";
        }

        if ($message) {
            $lines[] = '';
            $lines[] = $message;
        }

        $lines[] = '';
        $lines[] = 'Is this product available?';

        return implode("\n", $lines);
    }

    /**
     * Build a generic brand message.
     */
    public function buildBrandMessage(string $extra = ''): string
    {
        return trim("Hi Yazh Creations,\n\nI would like to know more about your collection.\n\n{$extra}");
    }

    /**
     * Build a wa.me deep link with a pre-filled message.
     */
    public function link(?string $message = null): string
    {
        $url = 'https://wa.me/'.$this->normalizedPhone(Setting::get('whatsapp_number') ?? '');

        return $message ? $url.'?'.http_build_query(['text' => $message]) : $url;
    }

    /**
     * Normalize a phone number to international digits for wa.me.
     */
    protected function normalizedPhone(?string $phone): string
    {
        if ($phone === null) {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (strlen($digits) === 10) {
            $digits = '91'.$digits;
        }

        return $digits;
    }

    /**
     * Raw international format for display purposes.
     */
    public function displayPhone(): string
    {
        $normalized = $this->normalizedPhone(Setting::get('whatsapp_number'));

        return $normalized ? '+'.$normalized : Setting::get('whatsapp_number') ?? '';
    }
}
