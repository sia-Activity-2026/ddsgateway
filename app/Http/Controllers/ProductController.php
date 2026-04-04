<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ProductController extends Controller
{
    protected function dbErrorResponse(Throwable $exception)
    {
        return response()->json([
            'error' => 'Database connection or table issue. Please run gateway migrations.',
            'details' => $exception->getMessage(),
            'code' => 500,
        ], 500);
    }

    protected function safeTableExists(): bool
    {
        try {
            return Schema::hasTable('products');
        } catch (QueryException $exception) {
            return false;
        }
    }

    public function index()
    {
        try {
            if (! $this->safeTableExists()) {
                return response()->json([
                    'data' => [],
                    'message' => 'products table not found. Run gateway migrations.',
                ], 200);
            }

            return response()->json(Product::all());
        } catch (Throwable $exception) {
            return $this->dbErrorResponse($exception);
        }
    }

    public function show($id)
    {
        try {
            if (! $this->safeTableExists()) {
                return response()->json([
                    'error' => 'products table not found. Run gateway migrations.',
                    'code' => 500,
                ], 500);
            }

            return response()->json(Product::findOrFail($id));
        } catch (Throwable $exception) {
            return $this->dbErrorResponse($exception);
        }
    }

    public function store(Request $request)
    {
        try {
            if (! $this->safeTableExists()) {
                return response()->json([
                    'error' => 'products table not found. Run gateway migrations.',
                    'code' => 500,
                ], 500);
            }

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
            ]);

            return response()->json(Product::create($data), 201);
        } catch (Throwable $exception) {
            return $this->dbErrorResponse($exception);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if (! $this->safeTableExists()) {
                return response()->json([
                    'error' => 'products table not found. Run gateway migrations.',
                    'code' => 500,
                ], 500);
            }

            $product = Product::findOrFail($id);
            $data = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|numeric',
                'stock' => 'sometimes|integer',
            ]);

            $product->update($data);
            return response()->json($product);
        } catch (Throwable $exception) {
            return $this->dbErrorResponse($exception);
        }
    }

    public function destroy($id)
    {
        try {
            if (! $this->safeTableExists()) {
                return response()->json([
                    'error' => 'products table not found. Run gateway migrations.',
                    'code' => 500,
                ], 500);
            }

            Product::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted']);
        } catch (Throwable $exception) {
            return $this->dbErrorResponse($exception);
        }
    }

    public function health()
    {
        try {
            DB::connection()->getPdo();

            if (! $this->safeTableExists()) {
                return response()->json([
                    'status' => 'ok',
                    'database' => DB::getDatabaseName(),
                    'table' => 'products not found',
                ], 200);
            }

            return response()->json([
                'status' => 'ok',
                'database' => DB::getDatabaseName(),
                'table' => 'products exists',
            ], 200);
        } catch (Throwable $exception) {
            return $this->dbErrorResponse($exception);
        }
    }
}