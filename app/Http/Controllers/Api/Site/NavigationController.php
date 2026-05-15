<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Slider;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    /**
     * Get navigation data for site header
     * Returns visible categories with sub-categories and all brands
     */
    public function index(Request $request)
    {
        // Get category limit from request (default: all)
        $categoryLimit = $request->get('category_limit');
        $subCategoryLimit = $request->get('sub_category_limit');

        // Get visible categories query
        $categoriesQuery = Category::where('visible', true)
            ->select('id', 'title', 'image');

        // Apply limit if specified
        if ($categoryLimit) {
            $categoriesQuery->limit($categoryLimit);
        }

        $categories = $categoriesQuery->get()->map(function ($category) use ($subCategoryLimit) {
            // Load sub-categories
            $subCategoriesQuery = $category->subCategories();

            // Apply sub-category limit if specified
            if ($subCategoryLimit) {
                $subCategoriesQuery->limit($subCategoryLimit);
            }

            $subCategories = $subCategoriesQuery->select('id', 'title', 'category_id')->get();

            return [
                'id' => $category->id,
                'title' => $category->title,
                'image_url' => $category->image_url,
                'sub_categories' => $subCategories->map(function ($subCategory) {
                    return [
                        'id' => $subCategory->id,
                        'title' => $subCategory->title,
                    ];
                }),
            ];
        });

        // Get all brands
        $brands = Brand::select('id', 'name', 'image')
            ->get()
            ->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'image_url' => $brand->image_url,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => [
                'categories' => $categories,
                'brands' => $brands,
            ],
            'message' => 'Navigation data retrieved successfully'
        ]);
    }

    /**
     * Get visible sliders for homepage
     */
    public function sliders()
    {
        $sliders = Slider::where('visible', true)
            ->orderBy('id', 'desc')
            ->select('id', 'title', 'description', 'image')
            ->get()
            ->map(function ($slider) {
                return [
                    'id' => $slider->id,
                    'title' => $slider->title,
                    'description' => $slider->description,
                    'image' => $slider->image_url,
                    'link' => $slider->link ?? '/products',
                ];
            });

        return response()->json([
            'status' => true,
            'data' => ['sliders' => $sliders],
            'message' => 'Sliders retrieved successfully'
        ]);
    }

    /**
     * Get all sub-categories
     */
    public function subCategories(Request $request)
    {
        // Get limit from request (default: all)
        $limit = $request->get('limit');

        $query = SubCategory::select('id', 'title', 'category_id')
            ->orderBy('title', 'asc');

        // Apply limit if specified
        if ($limit) {
            $query->limit($limit);
        }

        $subCategories = $query->get()
            ->map(function ($subCategory) {
                return [
                    'id' => $subCategory->id,
                    'title' => $subCategory->title,
                    'category_id' => $subCategory->category_id,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => ['sub_categories' => $subCategories],
            'message' => 'Sub-categories retrieved successfully'
        ]);
    }
}
