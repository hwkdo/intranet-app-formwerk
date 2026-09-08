<?php

declare(strict_types=1);

namespace Hwkdo\IntranetAppFormwerk\Http\Controllers;

use Hwkdo\IntranetAppFormwerk\Support\FormwerkModels;
use Hwkdo\IntranetAppFormwerk\Support\OnboardingFormRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class OnboardingRedirectController
{
    public function __invoke(Request $request, int|string $user, OnboardingFormRedirect $redirect): RedirectResponse
    {
        $userModel = FormwerkModels::user()::query()->findOrFail($user);

        if ((bool) ($userModel->onboarding_dokumente ?? false)) {
            return back()->with('message', 'Onboarding-Dokumente sind bereits erledigt.');
        }

        try {
            return redirect()->away($redirect->urlFor($userModel));
        } catch (RuntimeException $e) {
            throw new HttpException(503, $e->getMessage(), $e);
        }
    }
}
