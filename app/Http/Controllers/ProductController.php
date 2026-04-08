<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ProductController extends Controller
{
    protected function dbErrorResponse(Throwable $exception)
    {
        return response()->json([
            'error' => 'Database error. Ensure migrations have run.',
            'details' => $exception->getMessage(),
            'code' => 500,
        ], 500);
    }

    public function index(Request $request)
    {
        try {
            // Add pagination to improve performance and reduce payload
            $perPage = $request->get('per_page', 15);
            $products = Product::paginate($perPage);
            return response()->json($products);
        } catch (Throwable $exception) {
            return $this->dbErrorResponse($exception);
        }
    }

    public function show($id)
    {
        try {
            return response()->json(Product::findOrFail($id));
        } catch (Throwable $exception) {
            return $this->dbErrorResponse($exception);
        }
    }

    protected function validateProductRequest(Request $request, bool $isUpdate = false)
    {
        $data = $request->only(['name', 'description', 'price', 'stock']);
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

        return ['data' => $data, 'errors' => $errors];
    }

    public function store(Request $request)
    {
        try {
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
            $product = Product::findOrFail($id);
            $validation = $this->validateProductRequest($request, true);
            if (! empty($validation['errors'])) {
                return response()->json(['errors' => $validation['errors']], 422);
            }

            $product->update($validation['data']);
            return response()->json($product);
        } catch (Throwable $exception) {
            return $this->dbErrorResponse($exception);
        }
    }

    public function destroy($id)
    {
        try {
            Product::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted']);
        } catch (Throwable $exception) {
            return $this->dbErrorResponse($exception);
        }
    }

    public function health()
    {
        try {
            // Verify DB connection is alive
            DB::connection()->getPdo();

            // Check if products table exists (diagnostic only)
            $tableExists = Schema::hasTable('products');

            return response()->json([
                'status' => $tableExists ? 'ok' : 'error',
                'database' => DB::getDatabaseName(),
                'table' => $tableExists ? 'products exists' : 'products table missing - run migrations',
            ], $tableExists ? 200 : 503);
        } catch (Throwable $exception) {
            return $this->dbErrorResponse($exception);
        }
    }
}