<?php

namespace App\Http\Controllers\NetLicensing;

use App\Models\Auth\User\User;
use App\Models\NetLicensing\NlValidation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use NetLicensing\LicenseeService;
use NetLicensing\NetLicensingService;
use NetLicensing\ValidationParameters;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::with('roles')->sortable(['email' => 'asc'])->paginate();

        return view('netlicensing.permissions', ['users' => $users]);
    }

    public function repeat(User $user, Request $request)
    {
        $licenseeName = $user->getAttribute(config('netlicensing.defaults.licensee.name'));
        $licenseeNumber = $user->getAttribute(config('netlicensing.defaults.licensee.number'));
        $productNumber = config('netlicensing.product_number');

        $nlValidator = $user->nlValidation;
        $nlValidator = ($nlValidator) ? $nlValidator : new NlValidation(['user_id' => $user->id]);

        $validationParameters = new ValidationParameters();
        $validationParameters->setLicenseeName($licenseeName);
        $validationParameters->setProductNumber($productNumber);

        $validationResult = LicenseeService::validate(app('netlicensing')->context(), $licenseeNumber, $validationParameters);
        $validations = $validationResult->getValidations();

        foreach ($validations as &$validation) {
            $validation['valid'] = ($validation['valid'] == 'true') ? true : false;
        }

        $nlValidator->validation_result = $validations;

        //get ttl
        $nlValidator->ttl = new Carbon((string)NetLicensingService::getInstance()->lastCurlInfo()->response['ttl']);
        $nlValidator->save();

        return redirect()->back();
    }
}
