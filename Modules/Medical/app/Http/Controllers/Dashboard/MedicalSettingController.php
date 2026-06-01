<?php

namespace Modules\Medical\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MedicalSettingController extends Controller
{
    public function edit(Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $settings = $workspace->medicalSetting()->firstOrCreate(
            [
                'workspace_id' => $workspace->id,
            ],
            [
                'facility_type' => 'clinic',
                'display_name' => $workspace->name,
                'booking_enabled' => true,
                'default_currency' => 'EGP',
                'default_visit_duration' => 30,
                'primary_color' => '#2563eb',
                'secondary_color' => '#0f172a',
            ]
        );

        return view('medical::dashboard.settings.edit', compact(
            'workspace',
            'settings'
        ));
    }

    public function update(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $settings = $workspace->medicalSetting()->firstOrCreate([
            'workspace_id' => $workspace->id,
        ]);

        $data = $request->validate([
            'facility_type' => [
                'required',
                Rule::in(['clinic', 'medical_center', 'hospital', 'lab', 'scan_center']),
            ],

            'display_name' => ['nullable', 'string', 'max:190'],
            'description' => ['nullable', 'string'],

            'booking_enabled' => ['nullable', 'boolean'],
            'online_payment_enabled' => ['nullable', 'boolean'],
            'patient_portal_enabled' => ['nullable', 'boolean'],

            'default_currency' => ['required', 'string', 'max:10'],
            'default_visit_duration' => ['required', 'integer', 'min:5', 'max:240'],

            'allow_patient_files' => ['nullable', 'boolean'],
            'allow_results_download' => ['nullable', 'boolean'],

            'whatsapp_notifications_enabled' => ['nullable', 'boolean'],
            'sms_notifications_enabled' => ['nullable', 'boolean'],

            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
        ]);

        $settings->update([
            'facility_type' => $data['facility_type'],
            'display_name' => $data['display_name'] ?? null,
            'description' => $data['description'] ?? null,

            'booking_enabled' => $request->boolean('booking_enabled'),
            'online_payment_enabled' => $request->boolean('online_payment_enabled'),
            'patient_portal_enabled' => $request->boolean('patient_portal_enabled'),

            'default_currency' => $data['default_currency'],
            'default_visit_duration' => $data['default_visit_duration'],

            'allow_patient_files' => $request->boolean('allow_patient_files'),
            'allow_results_download' => $request->boolean('allow_results_download'),

            'whatsapp_notifications_enabled' => $request->boolean('whatsapp_notifications_enabled'),
            'sms_notifications_enabled' => $request->boolean('sms_notifications_enabled'),

            'primary_color' => $data['primary_color'] ?? '#2563eb',
            'secondary_color' => $data['secondary_color'] ?? '#0f172a',
        ]);

        return back()->with('success', 'تم حفظ إعدادات النظام الطبي بنجاح.');
    }
}