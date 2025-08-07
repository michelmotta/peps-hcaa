<?php

namespace Tests\Unit\Models;

use App\Models\File;
use App\Models\Library;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LibraryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function o_metodo_toSearchableArray_retorna_os_dados_corretos(): void
    {
        $libraryItem = Library::factory()->create([
            'title' => 'Manual de Procedimentos',
        ]);

        $expectedArray = [
            'title' => 'Manual de Procedimentos',
        ];

        $this->assertEquals($expectedArray, $libraryItem->toSearchableArray());
    }

    #[Test]
    public function o_item_da_biblioteca_pertence_a_um_arquivo(): void
    {
        $file = File::factory()->create();
        $libraryItem = Library::factory()->create(['file_id' => $file->id]);

        $this->assertInstanceOf(File::class, $libraryItem->file);
        $this->assertEquals($file->id, $libraryItem->file->id);
    }

    #[Test]
    public function o_item_da_biblioteca_pertence_a_um_usuario(): void
    {
        $user = User::factory()->create();
        $libraryItem = Library::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $libraryItem->user);
        $this->assertEquals($user->id, $libraryItem->user->id);
    }

    #[Test]
    public function o_acessor_created_at_formatted_retorna_a_data_formatada(): void
    {
        $date = Carbon::create(2025, 10, 5);
        $libraryItem = Library::factory()->create(['created_at' => $date]);

        $this->assertEquals('05/10/2025', $libraryItem->created_at_formatted);
    }
}
