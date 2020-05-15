<?php

namespace Tests\Feature\Command\Lien;

use App\Lien;
use Artisan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PruneOldLiensTest extends TestCase
{
    use DatabaseTransactions;

    public function testDeletesLienAfter5Years()
    {
        /** @var Lien $lien_1 */
        $lien_1 = factory(Lien::class)->create();
        Carbon::setTestNow(now()->addDay());

        /** @var Lien $lien_2 */
        $lien_2 = factory(Lien::class)->create();
        Carbon::setTestNow(now()->addYears(5)->subDay());

        Artisan::call('clientDomain:prune-old-liens');

        $this->assertSoftDeleted('liens', $lien_1->only('id'));
        $this->assertDatabaseHas('liens', [
            'id' => $lien_2->id,
            'deleted_at' => null,
        ]);

        Carbon::setTestNow(now()->addDay());
        Artisan::call('clientDomain:prune-old-liens');
        $this->assertSoftDeleted('liens', $lien_1->only('id'));
        $this->assertSoftDeleted('liens', $lien_2->only('id'));
    }
}
