<?php

namespace IlBronza\FileCabinet\Http\Controllers\FormAttaching;

use App\Http\Controllers\Controller;
use IlBronza\Category\Models\Category;
use IlBronza\FileCabinet\Helpers\AttachByCategoryTreeHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FormAttachByCategory extends Controller
{
    public function attachingDisambiguator(Model $model, Category $category)
    {
        $filecabinets = $model->getRootsFilecabinetByMainCategory($category);

        return view('filecabinet::filecabinet.attachingDisambiguator', compact('model', 'category', 'filecabinets'));
    }


    public function attachByCategory(Request $request, string $category, string $class, string $key)
    {
        $model = $class::findOrFail($key);
        $category = Category::getProjectClassname()::findOrFail($category);

        if(($model->haseRootsFilecabinetByMainCategory($category))&&(! $request->input('force', false)))
            return $this->attachingDisambiguator($model, $category);

        $helper = AttachByCategoryTreeHelper::attachByCategory(
            $model,
            $category
        );

        return redirect()->to(
            $helper->getFilecabinet()->getPopulateUrl()
        );
    }
}
