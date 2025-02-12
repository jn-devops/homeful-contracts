<?php

namespace App\Http\Controllers\Contract;

use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Actions\Contract\Consult;
use App\Actions\GetMatches;
use App\Classes\ProductOptions;
use Homeful\References\Models\Reference;
use Inertia\{Inertia, Response};
use Illuminate\Support\Arr;

class ConsultController extends Controller
{
    /**
     *  Verbosity
     *
     *  0 - SKU only
     *  1 - Product Attributes
     *  2 - Mortgage Attributes
     *
     * */
    const VERBOSE = 2;

    public function create(Request $request): Response
    {
        // The consultation functionality has been promptly integrated
        $contact_reference_code = Arr::get($request->all(), 'contact_reference_code');
        $reference = Consult::run($contact_reference_code);
        $reference = Reference::where('code', $reference->code)->first();
        return Inertia::render('Contract/Avail', [
            'buttonOptions' => $this->getOptions($reference->code),
            'contactData' => $reference->getContract()->getData()->contact,
            'contact_reference_code' => $contact_reference_code,
        ]);

        // return Inertia::render('Contract/Consult', $this->getProps($request)); // Previous implementation
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

    // From AvailController
    protected function getOptions($code): array
    {
        $options = [];
        if ($reference_code = $code) {
            if ($reference = Reference::where('code', $reference_code)->first()) {
                //TODO: cache this
                $matches = GetMatches::run($reference, config('homeful-contracts.records-limit', 3), self::VERBOSE);
                ProductOptions::setMatches($matches);
                $options = ProductOptions::records();
            }
        }

        return $options;
    }
}
