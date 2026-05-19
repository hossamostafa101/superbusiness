<?php

// app/Exports/AvailabilityRequestExport.php
namespace App\Exports;

use App\Models\AvailabilityRequest;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AvailabilityRequestExport implements FromArray, WithHeadings, WithTitle
{
    public function __construct(public AvailabilityRequest $req){}

    public function title(): string { return 'Availability'; }

    public function headings(): array
    {
        return ['م','اسم الفندق','المدينة','الدخول','الخروج','عدد الليالي','الغرف','ثنائي','ثلاثي','رباعي','خماسي','ملاحظات'];
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->req->items as $i=>$it) {
            $city  = $it->city ?? optional(optional($it->leg)->hotel)->city;
            $hotel = optional($it->hotel)->name ?? optional(optional($it->leg)->hotel)->name;
            $n2=$n3=$n4=$n5=null;
            $qty = (int)$it->qty_rooms;
            switch($it->room_type){
                case 'double': $n2=$qty; break;
                case 'triple': $n3=$qty; break;
                case 'quad':   $n4=$qty; break;
                case 'quint':  $n5=$qty; break;
                default: /* single */ ;
            }
            $rows[] = [
                $i+1, $hotel, $city, $it->checkin_date, $it->checkout_date, $it->nights,
                $qty, $n2, $n3, $n4, $n5, $it->reply_notes
            ];
        }
        return $rows;
    }
}
