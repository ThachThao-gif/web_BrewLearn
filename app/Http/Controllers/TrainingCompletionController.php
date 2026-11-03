<?php

namespace App\Http\Controllers;

use App\Models\AssessmentEligibility;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TrainingCompletionController extends Controller
{
    /**
     * Các vòng đào tạo lại đang thực hiện mà người dùng được giao phụ trách.
     */
    public function index(): View
    {
        $rounds = DB::table('retraining_rounds as r')->join('yeu_cau_dao_tao_lai as y', 'r.request_id', '=', 'y.id')
            ->join('users as u', 'y.user_id', '=', 'u.id')->where('r.owner_id', auth()->id())
            ->where('y.trang_thai', 'DaDuyet')->where('y.tien_trinh', 'DangThucHien')
            ->where('r.status', 'DangThucHien')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')->from('retraining_rounds as newer')
                    ->whereColumn('newer.request_id', 'r.request_id')->whereColumn('newer.round_number', '>', 'r.round_number');
            })
            ->select('r.*', 'y.user_id', 'y.ly_do', 'u.name as employee_name', 'u.ChucVu as employee_position')->orderByDesc('r.id')->get();
        foreach ($rounds as $round) {
            $round->eligibility = app(AssessmentEligibility::class)->round($round, $round->user_id, false);
        }

        return view('training.assignments', compact('rounds'));
    }

    /**
     * Người phụ trách xác nhận đã hoàn thành phần thực hành / theo dõi của vòng.
     */
    public function confirm(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate(['confirmation_note' => ['required', 'string', 'max:2000']]);
        DB::transaction(function () use ($id, $data): void {
            $round = DB::table('retraining_rounds')->where('id', $id)->where('owner_id', auth()->id())->lockForUpdate()->first();
            abort_unless($round, 404);
            abort_unless($round->status === 'DangThucHien', 409);
            $plan = DB::table('yeu_cau_dao_tao_lai')->find($round->request_id);
            abort_unless($plan && $plan->trang_thai === 'DaDuyet' && $plan->tien_trinh === 'DangThucHien', 409);
            abort_if(DB::table('retraining_rounds')->where('request_id', $round->request_id)->where('round_number', '>', $round->round_number)->exists(), 409);
            $eligibility = app(AssessmentEligibility::class)->round($round, $plan->user_id, false);
            if (! $eligibility['ready']) {
                throw ValidationException::withMessages(['training' => implode(' ', $eligibility['missing'])]);
            }
            DB::table('retraining_rounds')->where('id', $id)->update(['confirmed_by' => auth()->id(), 'confirmed_at' => now(), 'confirmation_note' => $data['confirmation_note'], 'updated_at' => now()]);
        });

        return back()->with('success', 'Đã xác nhận hoàn thành nội dung thực hành / theo dõi. Quản lý có thể tạo lịch đánh giá lại khi đủ điều kiện.');
    }
}
