<?php

namespace Tests\Feature\Helpers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait ModelHelpersTesting {

    public function testInsertData()
    {
        $model = $this->model();

        $data = $model::factory()->make()->toArray();

        if($this->model() instanceof User)
            $data['password'] = 123456;

        $model::create($data);

        $this->assertDatabaseHas($model->getTable(), $data);
    }


    abstract protected function model() : Model;
}
