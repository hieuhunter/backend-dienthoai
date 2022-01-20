<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\User;
use App\Models\Bill;
use App\Models\CTBill;
use App\Models\CTCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ql_dienthoaiController extends Controller
{
    public function product()
    {
        $sp = Product::limit(10)->get();
        return response()->json([
            'success' => true,
            'data' => $sp
        ], 200);
    }
    public function slproduct()
    {
        $slsp = Product::limit(5)->get();
        return response()->json([
            'success' => true,
            'data' => $slsp
        ], 200);
    }
    public function search(Request $request)
    {
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 1);

        $product = new Product;

        $searchTotal = $product->count();
        $search = $product->where('ten_sp', 'LIKE', '%' . $request->q . '%')->orWhere('gia',$request->q)->orderBy('id', 'desc')->skip(($offset - 1) * $limit)->limit($limit)->get();
        return response()->json([
            'success' => true,
            'data' => $search,
            'pagination' => [
                'total' => $searchTotal
            ] 
        ], 200);
    }
    public function ctproduct($id)
    {
        $sp = Product::where('id', $id)->first();
        return response()->json([
            'success' => true,
            'data' => $sp
        ]);
    }
    public function danhmuc()
    {
        $dm = Category::limit(9)->get();
        return response()->json([
            'success' => true,
            'data' => $dm
        ]);
    }
    public function thuonghieu()
    {
        $th = Brand::limit(14)->get();
        return response()->json([
            'success' => true,
            'data' => $th
        ]);
    }
    public function sptheodm($id)
    {
        $dm = Product::where('id_dm', $id)->get();
        return response()->json([
            'success' => true,
            'data' => $dm
        ]);
    }
    public function sptheoth($id)
    {
        $th = Product::where('id_th', $id)->get();
        return response()->json([
            'success' => true,
            'data' => $th
        ]);
    }

    public function addgiohang(Request $request)
    {
        $ktgh = Cart::where('id_kh', $request->user()->id)->get();

        if ($ktgh->count() < 1) {
            $payload = [
                'id_kh' => $request->user()->id,
            ];
            $gh = new Cart($payload);
            $gh->save();
            $idgh = $gh->id;
        } else {
            $idgh = $ktgh->first()->id;
        }

        $ktct = CTCart::where('id_sp', $request->id_sp)->where('id_gh', $idgh)->get();
        if ($ktct->count() > 0) {
            $slsp = $ktct->first()->so_luong + $request->so_luong;
            if ($slsp < 1) {
                $delete =  CTCart::where('id_sp', $request->id_sp)
                    ->where('id_gh', $idgh)
                    ->delete();
                if ($delete) {
                    $response = [
                        'success' => true,
                        'data' => [$delete, $idgh]
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'errorMessage' => 'error 500',
                    ];
                }
            } else {
                $giasp = $ktct->first()->gia + $request->so_luong * $request->gia;
                $updateCt = CTCart::where('id_sp', $request->id_sp)
                    ->where('id_gh', $idgh)
                    ->update(['so_luong' => $slsp, 'gia' => $giasp]);
                if ($updateCt) {
                    $response = [
                        'success' => true,
                        'data' => [$updateCt, $idgh]
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'errorMessage' => 'error 500',
                    ];
                }
            }
        } else {
            $slsp = $request->so_luong;
            $giasp = $request->so_luong * $request->gia;
            $ctgh = new CTCart;
            $ctgh->id_gh = $idgh;
            $ctgh->id_sp = $request->id_sp;
            $ctgh->so_luong = $slsp;
            $ctgh->gia = $giasp;
            $ctgh->save();
            if ($ctgh) {
                $response = [
                    'success' => true,
                    'data' => [$ctgh, $idgh]
                ];
            } else {
                $response = [
                    'success' => false,
                    'errorMessage' => 'error 500',
                ];
            }
        }
        return response()->json($response, 200);
    }
    public function danhsachcart(Request $request)
    {
        $list_cart = Cart::with('ctcart.product')->where('id_kh', $request->user()->id)->first();
        return response()->json([
            'success' => true,
            'data' => $list_cart
        ]);
    }
    public function xoacart(Request $request)
    {
        $ktgh = Cart::where('id_kh', $request->user()->id)->first();
        $ctgh = CTCart::where('id_gh', $ktgh->id)->where('id_sp', $request->id_sp)->delete();
        if ($ctgh) {
            $response = [
                'success' => true,
                'data' => $ctgh
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'error'
            ];
        }
        return response()->json($response, 200);
    }
    public function xoatatcagh(Request $request)
    {
        $ktgh = Cart::where('id_kh', $request->user()->id)->first();
        $ctgh = CTCart::where('id_gh', $ktgh->id)->delete();
        if ($ctgh) {
            $response = [
                'success' => true,
                'data' => $ctgh
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'error'
            ];
        }
        return response()->json($response, 200);
    }
    public function thanhtoan(Request $request)
    {
        $hoa_don = new Bill;
        $hoa_don -> id_kh = 1;
        $hoa_don -> ho_ten = $request->ho_ten;
        $hoa_don -> email = $request->email;
        $hoa_don -> sdt = $request->sdt;
        $hoa_don -> dia_chi = $request->dia_chi;
        $hoa_don -> ngay_dat = $request->ngay_dat;
        $hoa_don -> ngay_giao = $request->ngay_giao;
        $hoa_don -> da_duyet = 0;
        $hoa_don -> da_thanh_toan = 0;
        $hoa_don -> da_giao_hang = 0;
        $hoa_don ->save();
        $id_hd = $hoa_don->id;
        foreach($request->gio_hang as $key => $gio_hang)
        {
            $ct_hoadon = new CTBill();
            $ct_hoadon ->id_hd = $id_hd;
            $ct_hoadon ->id_sp = $gio_hang['product']['id'];
            $ct_hoadon ->gia = $gio_hang['gia'];
            $ct_hoadon ->so_luong = $gio_hang['so_luong'];
            $ct_hoadon ->save();
        }
        $ktgh = Cart::where('id_kh', $request->user()->id)->first();
        CTCart::where('id_gh', $ktgh->id)->delete();
        $response = [
            'success' => true,
            'data' => 'thanh_cong'
        ];
        return response()->json($response,200);
    }

    public function thongtin(Request $request)
    {
        $ktkh = User::select('id', 'ho_ten', 'user_name', 'email', 'sdt', 'dia_chi')->where('id', $request->user()->id)->first();
        if ($ktkh) {
            $response = [
                'success' => true,
                'data' => $ktkh
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'error'
            ];
        }
        return response()->json($response, 200);
    }

    public function capnhatthongtin(Request $request)
    {
        $rules = [
            'ho_ten' => 'required',
            'email' => 'required|unique:users,email,'.$request->user()->id,
            'sdt' => 'required'
        ];
        $messages = [
            'ho_ten.required' => 'Ho ten không được trống',
            'email.required' => 'email không được trống',
            'email.unique' => 'email đã tồn tại',
            'sdt.unique' => 'Sdt không được trống'
        ];
        $payload = [
            'ho_ten' => $request->ho_ten,
            'email' => $request->email,
            'sdt' => $request->sdt,
            'dia_chi' => $request->dia_chi,
        ];
        $validator = Validator::make($payload, $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 200);
        }

        $user = User::where('id', $request->user()->id)->firstOrFail();

        $user->ho_ten = $request->ho_ten;
        $user->email = $request->email;
        $user->sdt = $request->sdt;
        $user->dia_chi = $request->dia_chi;
        $user->save();

        return response()->json([
            'success' => true,
            'data' =>  $user
        ], 200);
    }
}