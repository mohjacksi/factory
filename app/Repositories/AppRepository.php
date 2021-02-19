<?php
/**
 * Created by PhpStorm.
 * User: amir
 * Date: 10/11/20
 * Time: 01:41 م
 */

namespace App\Repositories;


use App\Models\User\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Model;

class AppRepository
{
    use Changeable, Sortable;
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function update($id, array $attributes)
    {
        $row = $this->find($id);
        $row->update($attributes);
        return $row;
    }

    public function delete($id)
    {
        return $this->find($id)
            ->delete();
    }

    public function find($id)
    {
        $data = $this->model->where('id', $id)
            ->with($this->relations)
            ->where($this->conditions)
            ->orWhere($this->orConditions)
            ->select($this->columns)
            ->first();

        if (!$data) {
            throw ValidationException::withMessages([
                'id' => ['invalid id or invalid data'],
            ]);
        }
        return $data;
    }

    public function all()
    {
        return $this->model
            ->select($this->columns)
            ->with($this->relations)
            ->where($this->conditions)
            ->orWhere($this->orConditions)
            ->orderBy($this->sortBy, $this->sortOrder)
            ->get();
    }

    public function allUserOfOperator($area_id = 1)
    {
        return $this->model
            ->select($this->columns)
            ->with($this->relations)
            ->where($this->conditions)
            ->orWhere($this->orConditions)
            ->orderBy($this->sortBy, $this->sortOrder)
            ->whereHas('addresses',function ($q) use ($area_id){
                $q->where('area_id' , $area_id);
            })
            ->get();
    }

    public function paginate($pageCount = 15)
    {
        return $this->model->select($this->columns)
            ->with($this->relations)
            ->where($this->conditions)
            ->orWhere($this->orConditions)
            ->orderBy($this->sortBy, $this->sortOrder)
            ->paginate($pageCount);
    }

    public function paginateUserOfOperator($pageCount = 15 , $area_id = 1)
    {
        return $this->model->select($this->columns)
            ->with($this->relations)
            ->where($this->conditions)
            ->orWhere($this->orConditions)
            ->orderBy($this->sortBy, $this->sortOrder)
            ->whereHas('addresses',function ($q) use ($area_id){
                $q->where('area_id' , $area_id);
            })
            ->paginate($pageCount);
    }

    public function OrderPaginator($pageCount = 15)
    {
        return $this->model->select($this->columns)
            ->with($this->relations)
            ->whereNotNull($this->column)
            ->where($this->conditions)
            ->orWhere($this->orConditions)
            ->WhereColumn($this->column_conditions)
//            ->toSql();
            ->orderBy($this->sortBy, $this->sortOrder)
            ->paginate($pageCount);
    }

    public function OrderGetNextOrderPaginator($pageCount = 15)
    {
        return $this->model->select($this->columns)
            ->with($this->relations)
            ->where('is_assigned', 1)
            ->whereIn('status', [1, 2, 5])
            ->paginate($pageCount);
    }

    public function paginateInOrder($pageCount = 15)
    {
        return $this->model->select($this->columns)
            ->whereIn($this->original_id, $this->related_ids)
            ->with($this->relations)
            ->orderBy($this->sortBy, $this->sortOrder)
            ->where($this->conditions)
            ->orWhere($this->orConditions)
            ->paginate($pageCount);

    }

    public function allInOrder()
    {
        return $this->model->select($this->columns)
            ->whereIn($this->original_id, $this->related_ids)
            ->with($this->relations)
            ->orderBy($this->sortBy, $this->sortOrder)
            ->where($this->conditions)
            ->orWhere($this->orConditions);

    }

    public function paginateNotInOrder($pageCount = 15)
    {
        return $this->model->select($this->columns)
            ->whereNotIn($this->original_id, $this->related_ids)
            ->with($this->relations)
            ->orderBy($this->sortBy, $this->sortOrder)
            ->where($this->conditions)
            ->orWhere($this->orConditions)
            ->paginate($pageCount);
    }


    public function deleteByColumn($column, $value)
    {
        return $this->model->where($column, $value)->delete();
    }

    public function findByColumn($column, $value)
    {
        return $this->model->select($this->columns)
            ->where($this->conditions)
            ->orWhere($this->orConditions)
            ->where($column, $value)->first();
    }

    public function search($keyword, $column = 'name_en', $orColumn = null, $paginate = 1)
    {
        $this->setConditions([[$column, 'like', '%' . $keyword . '%']]);
        if ($orColumn) {
            $this->setOrConditions([[$orColumn, 'like', '%' . $keyword . '%']]);
        }

        if ($paginate != 1) {
            return $this->all();
        }
        return $this->paginate();
    }

}
