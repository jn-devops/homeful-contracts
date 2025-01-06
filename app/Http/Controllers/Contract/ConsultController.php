<?php

namespace App\Http\Controllers\Contract;

use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Actions\Contract\Consult;
use Inertia\{Inertia, Response};
use Illuminate\Support\Arr;

class ConsultController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('Contract/Consult', $this->getProps($request));
    }

    public function store(Request $request): RedirectResponse
    {
        /** TODO: validate this using api i.e, if it exists or not */
        $validated = Validator::validate($request->all(), [
            'contact_reference_code' => ['required', 'string', 'min:4']
        ]);
        $contact_reference_code = Arr::get($validated, 'contact_reference_code');

        /** a reference record is generated upon execution of this action */
        $reference = Consult::run($contact_reference_code);

        return redirect()->route('avail.create', ['reference' => $reference->code])->with('event', [
            'name' => 'reference',
            'data' => $reference->code
        ]);
    }

    /**
     * Process the props for Inertia immediate handover.
     * Get the contact reference code and then
     * retrieve the translation strings from
     * localization files located in /lang.
     * Use this to customize the views
     * in the future.
     *
     * @param Request $request
     * @return array
     */
    protected function getProps(Request $request): array
    {
        $contact_reference_code = Arr::get($request->all(), 'contact_reference_code');
        $contact_reference_label = __('labels.contact_reference');
        $contact_reference_placeholder = __('placeholders.contact_reference');
        $contact_reference_note = __('notes.contact_reference');

        return compact('contact_reference_code', 'contact_reference_label', 'contact_reference_placeholder', 'contact_reference_note');
    }
}
