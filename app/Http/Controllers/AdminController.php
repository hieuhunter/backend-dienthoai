<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use App\Models\CTBill;
use App\Models\Bill;
use Carbon\Carbon;
use App\Http\Requests\Admin\NewProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;	

class AdminController extends Controller
{

    public function login(Request $request)
    {
        $credentials = request(['user_name', 'password']);
        if (!auth()->attempt($credentials) || auth()->user()->is_admin === 0)
            return response()->json([
                'success' => false,
                'errors' => [
                    "user" => "User name or password does not exists"
                ]
            ], 400);
            
        $tokenResult = auth()->user()->createToken('Personal Access Token');
        return response()->json([
            'success' => true,
            'data' => [
                'id' => auth()->user()->id,
                'user_name' => auth()->user()->user_name,
                'ho_ten' => auth()->user()->ho_ten,
                'sdt' => auth()->user()->sdt,
                'dia_chi' => auth()->user()->dia_chi,
                'email' => auth()->user()->email,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        Auth::user()->token()->revoke();
        return response()->json([
            'success' => true,
            'data' => 'Logout success'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => auth()->user()
        ]);
    }

    public function sanpham(Request $request)
    {
        $limit = $request->get('limit', 5);
        $offset = $request->get('offset', 0);

        $product = new Product;

        $spCount = $product->get()->count();
        $spList = $product->orderBy('id', 'desc')->skip($offset)->take($limit)->get();
        return response()->json([
            'success' => true,
            'data' => $spList,
            'meta' => [
                'total' => $spCount
            ]
        ], 200);
    }

    public function ctProduct($id)
    {
        $sp = Product::where('id', $id)->first();
        return response()->json([
            'success' => true,
            'data' => $sp
        ]);
    }
    public function listProduct(Request $request)
    {
		$offset = $request->get('offset', 0);
		$limit = $request->get('limit', 10);
		$sortDirection = $request->get('sort_direction', 'desc');

		$sanphamQuery = new Product();

		if ($request->has('q')) {
			if ($request->q) {
				$sanphamQuery = $sanphamQuery
					->where('ten_sp', 'LIKE', '%' . $request->q . '%');
			}
		}

		if ($request->has('sort_by')) {
			if ($request->sort_by === 'categories') {
                $sanphamQuery = $sanphamQuery->with(['category' => function($q) {
                    return $q->orderBy('ten_dm', $sortDirection);
                }]);
			} else {
				$sanphamQuery = $sanphamQuery->orderBy($request->sort_by, $sortDirection);
			}
		}

		$sanphamTotal = $sanphamQuery->get()->count();

		$sanpham = $sanphamQuery->with('category')
				->orderBy('id', 'asc')
				->skip($offset)
				->take($limit)
				->get();

        return response()->json([
            'success' => true,
            'data' => $sanpham,
            'pagination' => [
                'total' => $sanphamTotal
            ]
        ], 200);
	}

    public function addnew_sp(Request $request)
	{
		$category = json_decode($request->category, true);
		$brand = json_decode($request->brand, true);

		$createPorduct = new Product;
		$createPorduct->id_dm = $category['id'];
		$createPorduct->id_th = $brand['id'];

		$createPorduct->ten_sp = $request->ten_sp;
		$createPorduct->gia_goc = $request->gia_goc;
        $createPorduct->gia = $request->gia;
        $createPorduct->so_luong = $request->so_luong;
        $createPorduct->thong_tin_man_hinh = $request->thong_tin_man_hinh;
        $createPorduct->cpu = $request->cpu;
        $createPorduct->ram = $request->ram;
        $createPorduct->camera_sau = $request->camera_sau;
        $createPorduct->camera_truoc = $request->camera_truoc;
        $createPorduct->bo_nho_trong = $request->bo_nho_trong;
        $createPorduct->the_nho_ngoai = $request->the_nho_ngoai;
        $createPorduct->pin = $request->pin;
        $createPorduct->he_dieu_hanh = $request->he_dieu_hanh;
        $createPorduct->kha_dung = $request->kha_dung;
		
		// Public folder
		if ($request->hasfile('hinh')) {
			$imageName = time() . '.' . $request->file('hinh')->extension();
			Storage::disk('images')->put($imageName, file_get_contents($request->file('hinh')));
			$createPorduct->hinh = '/images/sanpham/' . $imageName;
		}

		$createPorduct->save();
		$lastIdProduct = $createPorduct->id;
		$product = Product::where('id', $lastIdProduct)->firstOrFail();

		return response()->json([
            'success' => true,
            'data' => $product
        ], 200);
	}

	public function danhmuc()
    {
        $dm = Category::get();
        return response()->json([
            'success' => true,
            'data' => $dm
        ]);
    }

    public function thuonghieu()
    {
        $th = Brand::get();
        return response()->json([
            'success' => true,
            'data' => $th
        ]);
    }

    public function show_sp($id)
    {
        $sp = Product::where('id', $id)->with(['category', 'brand'])->first();

        return response()->json([
            'success' => true,
            'data' => $sp
        ]);
    }

    public function update_sp(Request $request, $id)
	{
		
		$category = json_decode($request->category, true);
		$brand = json_decode($request->brand, true);
        
        $updateProduct = Product::where('id', $id)->firstOrFail();
		$updateProduct->id_dm = $category['id'];
		$updateProduct->id_th = $brand['id'];
		$updateProduct->ten_sp = $request->ten_sp;
		$updateProduct->gia_goc = $request->gia_goc;
        $updateProduct->gia = $request->gia;
        $updateProduct->so_luong = $request->so_luong;
        $updateProduct->thong_tin_man_hinh = $request->thong_tin_man_hinh;
        $updateProduct->cpu = $request->cpu;
        $updateProduct->ram = $request->ram;
        $updateProduct->camera_sau = $request->camera_sau;
        $updateProduct->camera_truoc = $request->camera_truoc;
        $updateProduct->bo_nho_trong = $request->bo_nho_trong;
        $updateProduct->the_nho_ngoai = $request->the_nho_ngoai;
        $updateProduct->pin = $request->pin;
        $updateProduct->he_dieu_hanh = $request->he_dieu_hanh;
        $updateProduct->kha_dung = $request->kha_dung;
		// Public folder
		if ($request->hasfile('hinh')) {
			$oldImage = $updateProduct->hinh;
			if (Storage::disk('images')->exists($oldImage)) {
				Storage::disk('images')->delete($oldImage);
			}
			$imageName = time() . '.' . $request->file('hinh')->extension();
			Storage::disk('images')->put($imageName, file_get_contents($request->file('hinh')));
			$updateProduct->hinh = '/images/sanpham/' . $imageName;
		}

		$updateProduct->save();
		$lastIdProduct = $updateProduct->id;
        $product = Product::where('id', $lastIdProduct)->firstOrFail();
		return response()->json([
            'success' => true,
            'data' => $product
        ], 200);
	}

    public function destroy($id)
	{
		$deleteProduct = Product::where('id', $id)->firstOrFail();
		$deleteProduct->delete();
        return response()->json([
            'success' => true,
            'data' => $deleteProduct
        ], 200);
	}
    public function bill(Request $request)
    {
        $limit = $request->get('limit', 5);
        $offset = $request->get('offset', 0);

        $bill = new Bill;

        $billCount = $bill->get()->count();
        $billList = $bill->orderBy('id', 'desc')->skip($offset)->take($limit)->get();
        return response()->json([
            'success' => true,
            'data' => $billList,
            'meta' => [
                'total' => $billCount
            ]
        ], 200);
    }
    public function listBill(Request $request)
    {
		$offset = $request->get('offset', 0);
		$limit = $request->get('limit', 10);
		$sortDirection = $request->get('sort_direction', 'desc');

		$billQuery = new Bill();

		if ($request->has('q')) {
			if ($request->q) {
				$billQuery = $billQuery
					->where('ho_ten', 'LIKE', '%' . $request->q . '%');
			}
		}
		if ($request->has('sort_by')) {			
			$billQuery = $billQuery->orderBy($request->sort_by, $sortDirection);
			
		}
		$billTotal = $billQuery->get()->count();

		$bill = $billQuery->with('ctbill.product')
				->orderBy('id', 'asc')
				->skip($offset)
				->take($limit)
				->get();

        return response()->json([
            'success' => true,
            'data' => $bill,
            'pagination' => [
                'total' => $billTotal
            ]
        ], 200);
	}
    public function listUser(Request $request)
    {
		$offset = $request->get('offset', 0);
		$limit = $request->get('limit', 10);
		$sortDirection = $request->get('sort_direction', 'desc');

		$userQuery = new User();

		if ($request->has('q')) {
			if ($request->q) {
				$userQuery = $userQuery
					->where('ho_ten', 'LIKE', '%' . $request->q . '%');
			}
		}
		if ($request->has('sort_by')) {			
			$userQuery = $userQuery->orderBy($request->sort_by, $sortDirection);
			
		}
		$userTotal = $userQuery->get()->count();
        $khList = $userQuery->orderBy('id', 'desc')->skip($offset)->take($limit)->get();
        return response()->json([
            'success' => true,
            'data' => $khList,
            'pagination' => [
                'total' => $userTotal
            ]
        ], 200);
	}
   
}
