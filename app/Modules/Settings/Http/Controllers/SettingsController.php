<?php

declare(strict_types=1);

namespace App\Modules\Settings\Http\Controllers;

use App\Modules\Settings\Http\Requests\UpdateSettingsRequest;
use App\Modules\Settings\Services\SettingsService;
use App\Shared\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

class SettingsController
{
    public function __construct(
        private readonly SettingsService $settingsService,
    ) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->settingsService->all());
    }

    public function update(UpdateSettingsRequest $request): JsonResponse
    {
        /** @var array<string, mixed> $settings */
        $settings = $request->validated('settings');

        $updated = $this->settingsService->updateMany($settings);

        return ApiResponse::success($updated);
    }
}
