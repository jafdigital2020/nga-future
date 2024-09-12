<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function training()
    {
        return view('admin.training.training');
    }

    public function trainers()
    {
        return view('admin.training.trainers');
    }

    public function trainingType()
    {
        return view('admin.training.trainingtype');
    }
}
