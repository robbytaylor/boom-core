<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Group as GroupModel;
use BoomCMS\Repositories\Group as GroupRepository;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class GroupTest extends AbstractTestCase
{
    public function testDelete()
    {
        $model = m::mock(GroupModel::class);
        $model
            ->shouldReceive('delete');

        $repository = new GroupRepository($model);

        $this->assertEquals($repository, $repository->delete($model));
    }

    public function testFind()
    {
        $id = 1;

        $model = m::mock(GroupModel::class);
        $model
            ->shouldReceive('find')
            ->with($id);

        $repository = new GroupRepository($model);

        $repository->find($id);
    }

    public function testSave()
    {
        $repository = new GroupRepository();

        $model = m::mock(GroupModel::class);
        $model->shouldReceive('save');

        $repository->save($model);
    }
}
