<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

    protected function validateProductRequest(Request $request, bool $isUpdate = false)
    {
        $data = $request->only(['name', 'description', 'price', 'stock', 'updated_at']);
        $errors = [];

        if (! $isUpdate || $request->has('name')) {
            if (! isset($data['name']) || trim($data['name']) === '') {
                $errors['name'][] = 'The name field is required.';
            } elseif (! is_string($data['name'])) {
                $errors['name'][] = 'The name must be a string.';
            } elseif (mb_strlen($data['name']) > 255) {
                $errors['name'][] = 'The name may not be greater than 255 characters.';
            }
        }

        if ($request->has('description')) {
            if ($data['description'] !== null && ! is_string($data['description'])) {
                $errors['description'][] = 'The description must be a string.';
            }
        }

        if (! $isUpdate || $request->has('price')) {
            if (! isset($data['price']) || trim($data['price']) === '') {
                $errors['price'][] = 'The price field is required.';
            } elseif (! is_numeric($data['price'])) {
                $errors['price'][] = 'The price must be a number.';
            }
        }

        if (! $isUpdate || $request->has('stock')) {
            if (! isset($data['stock']) || trim($data['stock']) === '') {
                $errors['stock'][] = 'The stock field is required.';
            } elseif (filter_var($data['stock'], FILTER_VALIDATE_INT) === false) {
                $errors['stock'][] = 'The stock must be an integer.';
            }
        }

        if ($request->has('updated_at')) {
            if ($data['updated_at'] !== null && ! is_string($data['updated_at'])) {
                $errors['updated_at'][] = 'The updated_at must be a string.';
            } else {
                try {
                    Carbon::parse($data['updated_at']);
                } catch (Throwable $exception) {
                    $errors['updated_at'][] = 'The updated_at is not a valid date.';
                }
            }
        }

        return ['data' => $data, 'errors' => $errors];
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

            $validation = $this->validateProductRequest($request, false);
            if (! empty($validation['errors'])) {
                return response()->json(['errors' => $validation['errors']], 422);
            }

            return response()->json(Product::create($validation['data']), 201);
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
            $validation = $this->validateProductRequest($request, true);
            if (! empty($validation['errors'])) {
                return response()->json(['errors' => $validation['errors']], 422);
            }

            if (array_key_exists('updated_at', $validation['data'])) {
                $product->timestamps = false;
            }

            $product->update($validation['data']);

            if (array_key_exists('updated_at', $validation['data'])) {
                $product->timestamps = true;
            }

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