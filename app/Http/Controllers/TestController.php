<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class TestController extends Controller
{
    private function getCurrentUserId(Request $request)
    {
        return $request->input('user_id') ?? $request->query('user_id') ?? Auth::id() ?? 1;
    }

    /**
     * Hiển thị giao diện làm bài kiểm tra
     */
    public function show(Request $request, $id)
    {
        $userId = $this->getCurrentUserId($request);
        $user = User::find($userId) ?? User::first();

        // Lấy thông tin bài kiểm tra
        $test = DB::table('bai_kiem_tra')
            ->where('MaBaiKiemTra', $id)
            ->first();

        if (!$test) {
            return redirect()->route('tests.index', ['user_id' => $userId])
                             ->with('error', 'Bài kiểm tra không tồn tại!');
        }

        // Lấy danh sách câu hỏi thuộc bài kiểm tra
        $questions = DB::table('cau_hoi_kiem_tra')
            ->where('MaBaiKiemTra', $id)
            ->get();

        return view('tests.show', compact('test', 'questions', 'user'));
    }

    /**
     * Xử lý chấm điểm khi người dùng nộp bài (AJAX)
     */
    public function submit(Request $request, $id)
    {
        $userId = $this->getCurrentUserId($request);

        $test = DB::table('bai_kiem_tra')
            ->where('MaBaiKiemTra', $id)
            ->first();

        if (!$test) {
            return response()->json([
                'success' => false,
                'message' => 'Bài kiểm tra không tồn tại!'
            ], 404);
        }

        // Lấy danh sách câu hỏi để đối chiếu đáp án
        $questions = DB::table('cau_hoi_kiem_tra')
            ->where('MaBaiKiemTra', $id)
            ->get()
            ->values(); // Reset chỉ số mảng về 0, 1, 2... tương ứng với index JS

        if ($questions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Bài kiểm tra này chưa có câu hỏi!'
            ], 400);
        }

        // Mảng câu trả lời gửi từ Blade View: { "0": "A", "1": "C", ... }
        $userAnswers = $request->input('answers', []);

        $correctCount = 0;
        $totalQuestions = $questions->count();

        foreach ($questions as $index => $q) {
            // Kiểm tra theo index câu hỏi gửi lên từ JS
            if (isset($userAnswers[$index])) {
                $selectedOption = strtoupper(trim($userAnswers[$index]));
                $correctOption  = strtoupper(trim($q->DapAnDung));

                if ($selectedOption === $correctOption) {
                    $correctCount++;
                }
            }
        }

        // Tính điểm trên thang 100
        $score = round(($correctCount / $totalQuestions) * 100);
        $passScore = $test->DiemChuan ?? 80;
        $isPassed = $score >= $passScore;

        // Lưu hoặc cập nhật kết quả vào bảng ket_qua_kiem_tra
        DB::table('ket_qua_kiem_tra')->updateOrInsert(
            [
                'user_id' => $userId,
                'MaBaiKiemTra' => $id,
            ],
            [
                'Diem' => $score,
                'TrangThai' => $isPassed ? 'Dat' : 'KhongDat',
                'SoCauDung' => $correctCount,
                'TongSoCau' => $totalQuestions,
                'updated_at' => now(),
                'created_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'score' => $score,
            'correct_count' => $correctCount,
            'total_questions' => $totalQuestions,
            'message' => $isPassed 
                ? "Chúc mừng! Bạn đạt {$score}/100 điểm ({$correctCount}/{$totalQuestions} câu)." 
                : "Bạn đạt {$score}/100 điểm. Chưa đủ điểm chuẩn ({$passScore} điểm) để vượt qua."
        ]);
    }
}