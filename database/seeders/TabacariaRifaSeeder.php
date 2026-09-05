<?php

namespace Database\Seeders;

use App\Models\Rifa;
use App\Models\Slideshow;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TabacariaRifaSeeder extends Seeder
{
    /**
     * Seed Tobacco / Headshop raffles.
     */
    public function run(): void
    {
        // 1. Banner Slideshow
        Slideshow::updateOrCreate(
            ['alt' => 'Banner Kit Marola VIP'],
            [
                'image' => 'slideshows/banner-kitmarola.jpg',
                'order' => 1,
            ]
        );

        // 2. Rifas
        $rifas = [
            [
                'title' => 'Boné RAW Original + Case Completo com Cuia, Tesoura, Seda e Porta Beck',
                'description' => '<p>🔥 <strong>Kit Marola Headshop RAW Oficial</strong> 🔥</p><p>Garanta sua chance de levar esse kit completo e exclusivo por apenas <strong>R$ 2,99</strong>!</p><ul><li>🧢 <strong>Boné RAW Original</strong> - Aba curva / snapback com acabamento premium e porta-piteira embutido.</li><li>💼 <strong>Case Antiodor Reforçado</strong> - Espaço dedicado para todos os seus acessórios com fechamento hermético.</li><li>🥣 <strong>Cuia de Silicone Especial</strong> - Material antiaderente de alta durabilidade e fácil higienização.</li><li>✂️ <strong>Tesoura de Precisão em Inox</strong> - Ponta arredondada para cortes precisos.</li><li>📜 <strong>Seda RAW King Size Slim</strong> - Papel de cânhamo 100% natural, queima lenta e sem químicos.</li><li>🎋 <strong>Porta Beck / Tubete Hermético</strong> - Proteção à prova de água e odores para transporte seguro.</li></ul><p><em>⚡ Sorteio realizado logo após 100% das cotas vendidas! Pagamento instantâneo via PIX com baixa automática e números da sorte enviados no seu WhatsApp.</em></p>',
                'price' => 2.99,
                'total_numbers_available' => 100,
                'buy_min' => 1,
                'buy_max' => 20,
                'thumbnail' => 'rifas/kit-raw.jpg',
                'raffle' => 'Sorteio realizado imediatamente após 100% das cotas vendidas.',
                'status' => Rifa::STATUS_PUBLISHED,
                'published_at' => now(),
                'expired_at' => null,
                'progress_percentage' => 45,
                'ranking_buyer' => true,
            ],
            [
                'title' => 'Kit Narguilé Completo Amazon Hookah + Prato + Vaso + Essências Premium',
                'description' => '<p>Leve para casa um <strong>Kit Narguilé Completo Amazon Hookah Premium</strong>!</p><ul><li>Stem Amazon Hookah em Alumínio Anodizado</li><li>Vaso de Vidro Reforçado com Detalhes Ouro</li><li>Prato de Alumínio Reforçado</li><li>Mangueira de Silicone Lavável com Piteira de Alumínio</li><li>Pack com 5 Essências Zomo/Ziggy + 1kg de Carvão de Coco</li></ul><p><em>Pagamento instantâneo via PIX com confirmação no WhatsApp!</em></p>',
                'price' => 2.99,
                'total_numbers_available' => 100,
                'buy_min' => 1,
                'buy_max' => 20,
                'thumbnail' => 'rifas/narguile.jpg',
                'raffle' => 'Sorteio ao vivo após a venda de 100% das cotas.',
                'status' => Rifa::STATUS_PUBLISHED,
                'published_at' => now(),
                'expired_at' => null,
                'progress_percentage' => 68,
                'ranking_buyer' => true,
            ],
            [
                'title' => 'Pod System Vaporesso Luxe XR Max + Combo 3 Juices Premium',
                'description' => '<p>Potência e sabor incomparáveis no mundo do Vaping!</p><ul><li>Vaporesso Luxe XR Max 80W com Bateria de 2800mAh</li><li>2 Cartuchos (DTL e RDL) + Coils GTX Mesh</li><li>Combo 3 Juices Importados de 60ml cada</li></ul><p><em>Rifa rápida com cotas promocionais por R$ 2,99 cada!</em></p>',
                'price' => 2.99,
                'total_numbers_available' => 100,
                'buy_min' => 1,
                'buy_max' => 20,
                'thumbnail' => 'rifas/vape.jpg',
                'raffle' => 'Sorteio ao vivo após 100% das cotas vendidas.',
                'status' => Rifa::STATUS_PUBLISHED,
                'published_at' => now(),
                'expired_at' => null,
                'progress_percentage' => 24,
                'ranking_buyer' => true,
            ],
        ];

        foreach ($rifas as $item) {
            $item['slug'] = Str::slug($item['title']);
            Rifa::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
