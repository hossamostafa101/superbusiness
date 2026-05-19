<?php
// app/Mail/AvailabilityRequestMail.php
namespace App\Mail;

use App\Models\AvailabilityRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class AvailabilityRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AvailabilityRequest $req){}

    public function build()
    {
        $signedUrl = URL::signedRoute('agent.reply.form', ['token' => $this->req->public_token]);

        // (اختياري) مرفق CSV سريع
        $csv = $this->buildCsv();

        return $this->subject('طلب توافر غرف: '.$this->req->request_no)
            ->view('mail.availability_request', [
                'req' => $this->req,
                'signedUrl' => $signedUrl,
            ])
            ->attachData($csv, "availability-{$this->req->request_no}.csv", [
                'mime' => 'text/csv',
            ]);
    }

    protected function buildCsv(): string
    {
        $out = fopen('php://temp', 'r+');
        fputcsv($out, ['City','Hotel','Check-in','Check-out','Nights','Room Type','Audience','Qty Rooms']);
        foreach ($this->req->items as $it) {
            fputcsv($out, [
                $it->city ?? optional(optional($it->leg)->hotel)->city,
                optional($it->hotel)->name ?? optional(optional($it->leg)->hotel)->name,
                $it->checkin_date, $it->checkout_date,
                $it->nights,
                $it->room_type, $it->audience, $it->qty_rooms
            ]);
        }
        rewind($out);
        return stream_get_contents($out);
    }
}
