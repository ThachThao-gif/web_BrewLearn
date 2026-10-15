<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

#[Signature('brewlearn:calculate-risk')]
#[Description('Chạy chương trình Python tính cảnh báo nguy cơ theo bộ luật và lưu kết quả')]
class CalculateRiskWarnings extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $result = Process::path(base_path())
            ->timeout(config('brewlearn.risk_timeout'))
            ->run([config('brewlearn.risk_python'), '-X', 'utf8', config('brewlearn.risk_script')]);

        $this->output->write($result->output());

        if ($result->failed()) {
            $this->error('Tính cảnh báo nguy cơ thất bại (mã thoát '.$result->exitCode().').');
            $this->output->write($result->errorOutput());

            return self::FAILURE;
        }

        $this->info('Đã cập nhật cảnh báo nguy cơ.');

        return self::SUCCESS;
    }
}
