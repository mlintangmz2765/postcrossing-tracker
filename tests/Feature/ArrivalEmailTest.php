<?php

namespace Tests\Feature;

use App\Livewire\ReceiveConfirm;
use App\Models\Country;
use App\Models\Postcard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class ArrivalEmailTest extends TestCase
{
    use RefreshDatabase;

    private Postcard $card;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::create(['username' => 'tester', 'password' => 'secret']);
        $country = Country::create(['nama_indonesia' => 'Jerman', 'nama_inggris' => 'Germany', 'kode_iso' => 'DE']);

        $this->card = Postcard::create([
            'user_id' => $user->id,
            'uid' => (string) Str::ulid(),
            'type' => 'sent',
            'postcard_id' => 'US-1234567',
            'country_id' => $country->id,
            'tanggal_kirim' => '2024-03-15',
        ]);
    }

    private function useMailConfig(?string $ownerEmail, ?string $fromAddress): void
    {
        config([
            'mail.default' => 'array',
            'app.owner_email' => $ownerEmail,
            'mail.from.address' => $fromAddress,
            'mail.from.name' => 'Tracker',
        ]);
        Mail::purge('array'); // rebuild the mailer so it picks up the "from" above
    }

    private function confirm(): void
    {
        Livewire::test(ReceiveConfirm::class, ['uid' => $this->card->uid])
            ->set('message', 'Arrived safely!')
            ->call('confirm')
            ->assertSet('justConfirmed', true);
    }

    private function sentMessages()
    {
        return Mail::mailer('array')->getSymfonyTransport()->messages();
    }

    public function test_skips_the_email_and_logs_a_warning_when_owner_email_is_unset(): void
    {
        $this->useMailConfig(null, 'tracker@example.test');
        Log::spy();

        $this->confirm();

        $this->assertCount(0, $this->sentMessages());
        Log::shouldHaveReceived('warning')->once()
            ->with('Arrival email not sent: OWNER_EMAIL is not set.', ['postcard_id' => $this->card->id]);
        // The recipient's confirmation is still recorded.
        $this->assertNotNull($this->card->fresh()->tanggal_terima);
        $this->assertSame('Arrived safely!', $this->card->fresh()->pesan_penerima);
    }

    public function test_skips_the_email_when_the_sender_address_is_unset(): void
    {
        $this->useMailConfig('owner@example.test', null);
        Log::spy();

        $this->confirm();

        $this->assertCount(0, $this->sentMessages());
        Log::shouldHaveReceived('warning')->once()
            ->with('Arrival email not sent: MAIL_FROM_ADDRESS is not set.', ['postcard_id' => $this->card->id]);
    }

    public function test_sends_to_owner_email_from_the_configured_sender(): void
    {
        $this->useMailConfig('owner@example.test', 'tracker@example.test');

        $this->confirm();

        $messages = $this->sentMessages();
        $this->assertCount(1, $messages);

        $email = $messages->first()->getOriginalMessage();
        $this->assertSame(['owner@example.test'], array_map(fn ($a) => $a->getAddress(), $email->getTo()));
        $this->assertSame('tracker@example.test', $email->getFrom()[0]->getAddress());
        $this->assertSame('Tracker', $email->getFrom()[0]->getName());
        $this->assertStringContainsString('Germany', $email->getSubject());
        $this->assertStringContainsString('Arrived safely!', $email->getHtmlBody());
    }
}
