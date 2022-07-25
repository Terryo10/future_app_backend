<?php

namespace App\Admin\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Encore\Admin\Admin;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;

class ArticleController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Article';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Article());

        $grid->column('id', __('Id'));
        $grid->column('heading', __('Heading'));
        $grid->column('topic', __('Topic'));
        $grid->column('image_path', __('Image'))->image();
        $grid->column('category_id', __('Category'))->display(function ($value) {
            $category = Category::find($value);
            return $category->name;
        });
        $grid->column('user_id', __('Owned By'))->display(function ($value) {
            $user = Administrator::find($value);
            return $user->name;
        });
        $grid->column('body')->display(function ($value) {
            return substr(strip_tags($value), 0, 100);
        });
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Article::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('heading', __('Heading'));
        $show->field('topic', __('Topic'));
        $show->field('image_path', __('Image path'));
        $show->field('category_id', __('Category id'));
        $show->field('user_id', __('User id'));
        $show->field('body', __('Body'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Article());

        $form->text('heading', __('Heading'))->required();
        $form->text('topic', __('Topic'))->required();
        $form->image('image_path', __('Image path'))->required();
        $form->select('category_id', __('Select Category '))->options(Category::all()->pluck('name', 'id'))->required();;
        $form->hidden('user_id', __('User id'))->value(Auth::user()->id);
        $form->ckeditor('body', __('Body'))->required();

        return $form;
    }
}
