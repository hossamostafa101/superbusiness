<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Admin\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService
    ) {
        $this->middleware('permission:payments.view')->only(['index', 'show']);
        $this->middleware('permission:payments.approve')->only(['approve']);
        $this->middleware('permission:payments.reject')->only(['reject']);
    }

   public function index(Request $request)
{
    $payments = Payment::query()
        ->with([
            'workspace:id,name,slug',
            'plan:id,name,slug',
            'approvedBy:id,name,email',
        ])
        ->when($request->filled('status'), function ($query) use ($request) {
            $query->where('status', $request->input('status'));
        })
        ->when($request->filled('provider'), function ($query) use ($request) {
            $query->where('provider', $request->input('provider'));
        })
        ->when($request->filled('search'), function ($query) use ($request) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('provider_payment_id', 'like', "%{$search}%")
                    ->orWhere('provider_reference', 'like', "%{$search}%")
                    ->orWhereHas('workspace', function ($workspaceQuery) use ($search) {
                        $workspaceQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%");
                    })
                    ->orWhereHas('plan', function ($planQuery) use ($search) {
                        $planQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%");
                    });
            });
        })
        ->latest('id')
        ->paginate(15)
        ->withQueryString();

    return view('admin.sections.payments.index', compact('payments'));
}

    public function show(Payment $payment)
    {
        $payment->load([
            'workspace.owner',
            'plan',
            'subscription',
            'approvedBy',
        ]);

        return view('admin.sections.payments.show', compact('payment'));
    }

    public function approve(Payment $payment)
    {
        try {
            $this->paymentService->approveManualPayment($payment, auth('admin')->id());

            return redirect()
                ->route('admin.payments.show', $payment)
                ->with('success', 'تم اعتماد الدفع وتفعيل الاشتراك.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $this->paymentService->rejectManualPayment(
                payment: $payment,
                adminId: auth('admin')->id(),
                reason: $data['reason'] ?? null
            );

            return redirect()
                ->route('admin.payments.show', $payment)
                ->with('success', 'تم رفض الدفع.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}