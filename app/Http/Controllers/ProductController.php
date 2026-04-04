<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
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

    protected function ensureProductsTable(): bool
    {
        try {
            if (Schema::hasTable('products')) {
                return true;
            }

            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->integer('stock')->default(0);
                $table->timestamps();
            });

            return true;
        } catch (Throwable $exception) {
            return false;
        }
    }

    public function index()
    {
        try {
            if (! $this->ensureProductsTable()) {
                return response()->json([
                    'error' => 'Could not create or access products table. Check gateway DB permissions.',
                    'code' => 500,
                ], 500);
            }

            return response()->json(Product::all());
        } catch (Throwable $exception) {
            return $this->dbErrorResponse($exception);
        }
    }

    public function show($id)
    {
        try {
            if (! $this->ensureProductsTable()) {
                return response()->json([
                    'error' => 'Could not create or access products table. Check gateway DB permissions.',
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
            if (! $this->ensureProductsTable()) {
                return response()->json([
                    'error' => 'Could not create or access products table. Check gateway DB permissions.',
                    'code' => 500,
                ], 500);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return response()->json(Product::create($validator->validated()), 201);
        } catch (Throwable $exception) {
            return $this->dbErrorResponse($exception);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if (! $this->ensureProductsTable()) {
                return response()->json([
                    'error' => 'Could not create or access products table. Check gateway DB permissions.',
                    'code' => 500,
                ], 500);
            }

            $product = Product::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|numeric',
                'stock' => 'sometimes|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $product->update($validator->validated());
            return response()->json($product);
        } catch (Throwable $exception) {
            return $this->dbErrorResponse($exception);
        }
    }

    public function destroy($id)
    {
        try {
            if (! $this->ensureProductsTable()) {
                return response()->json([
                    'error' => 'Could not create or access products table. Check gateway DB permissions.',
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

            if (! Schema::hasTable('products')) {
                if ($this->ensureProductsTable()) {
                    return response()->json([
                        'status' => 'ok',
                        'database' => DB::getDatabaseName(),
                        'table' => 'products created',
                    ], 200);
                }

                return response()->json([
                    'status' => 'error',
                    'database' => DB::getDatabaseName(),
                    'table' => 'products not found and could not be created',
                ], 500);
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