<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashBoard\Package\PackageRequest;
use App\Http\Requests\DashBoard\Package\UpdatePackageRequest;
use App\Http\Resources\PackageResource;
use App\Models\Payment\Package;
use App\Models\Payment\Payment;
use App\Services\Payment\PaymobPaymentService;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $paymentService;

    public function __construct(PaymobPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $packages = Package::latest()->get();

        return response()->json([
            'status'  => true,
            'message' => 'Packages retrieved successfully',
            'data'    => PackageResource::collection($packages),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PackageRequest $request)
    {

        $package = Package::create($request->only(['name', 'price', 'coins', 'is_active']));

        return response()->json([
            'status'  => true,
            'message' => 'Package created successfully',
            'data'    => new PackageResource($package),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json([
                'status'  => false,
                'message' => 'Package not found',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Package retrieved successfully',
            'data'    => new PackageResource($package),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePackageRequest $request, string $id)
    {

        $package = Package::find($id);

        if (!$package) {
            return response()->json([
                'status'  => false,
                'message' => 'Package not found',
            ], 404);
        }

        $package->update($request->only(['name', 'price', 'coins', 'is_active']));

        return response()->json([
            'status'  => true,
            'message' => 'Package updated successfully',
            'data'    => new PackageResource($package),
        ], 200);
    }

    /**
     * Toggle is_active status.
     */
    public function changeStatus(Request $request, string $id)
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $package = Package::find($id);

        if (!$package) {
            return response()->json([
                'status'  => false,
                'message' => 'Package not found',
            ], 404);
        }

        $package->update(['is_active' => $request->is_active]);

        return response()->json([
            'status'  => true,
            'message' => 'Package status updated successfully',
            'data'    => new PackageResource($package),
        ], 200);
    }



    public function destroy(string $id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json([
                'status'  => false,
                'message' => 'Package not found',
            ], 404);
        }

        $package->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Package soft deleted successfully',
        ], 200);
    }

    public function pay($packageId)
    {
        $user = auth()->user();

        $package = Package::where('is_active', true)->find($packageId);

        if (!$package) {
            return response()->json([
                'status'  => false,
                'message' => 'Package not found',
            ], 404);
        }


        $payment = $this->paymentService->sendPayment($user, $package);

        return response()->json([
            'success' => true,
            'payment_url' => $payment['url'],
            'message' => 'Redirect user to payment page'
        ]);
    }

    public function totalRevenue()
    {
        $packagesRevenue = Payment::where('status', 'paid')
            ->where('type', 'package')
            ->sum('amount');

        $totalProfit = $packagesRevenue;

        return response()->json([
            'status'  => true,
            'message' => 'Total revenue retrieved successfully',
            'data'    => [
                'total_profit'     => $totalProfit,
                'revenue_details'  => [
                    'from_packages' => $packagesRevenue,
                ],
            ]
        ], 200);
    }
}
