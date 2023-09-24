<?php

namespace NamaaSolutions\CrudGenerator\Controllers;

use NamaaSolutions\CrudGenerator\Messages;
use NamaaSolutions\CrudGenerator\Traits\CrudMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrudController extends Controller
{
    use CrudMiddleware {
        CrudMiddleware::__construct as private __crudMiddlewareConstruct;
    }

    protected $premissionSuffix;

    protected $name;

    protected $model;

    protected $createRequest;

    protected $request;

    protected $modelClass;

    protected $hasPermissions = true;


    protected $showWith = [], $indexWith = [];

    public function __construct()
    {
        $this->model = new $this->modelClass;
        $this->premissionSuffix = Str::plural($this->name);

        if ($this->hasPermissions)
            $this->__crudMiddlewareConstruct();
    }

    public function index(Request $request)
    {
        $res = $this->model
            ->with($this->indexWith)
            ->dynamicFilter([])
            ->orderByCrud($request->sort_column ?? 'uuid', $request->sort_direction ?? 'DESC');

        $res = $this->addIndexFilters($res);
        $res  = $res->paginate($request->pageSize);
        return $this->SuccessResponse([
            Str::plural($this->name) => $res,
            'helper' => \App\Models\Tenants\Helper::whereName(Str::plural($this->name))->get()->first(),
            'extra_data' => $this->model->getIndexExtraData()
        ]);
    }

    public function addIndexFilters($query)
    {
        return $query;
    }

    public function show($id)
    {
        $modelResult = $this->model
            ->with($this->showWith)
            ->findOrFail($id);

        return $this->SuccessResponse([
            'item' => $modelResult,
        ]);
    }

    public function store(Request $request)
    {
        app($this->request);


        DB::beginTransaction();
        try {
            $data = $this->beforeStore($request);

            $result = $this->model->create($data);

            $this->afterSave($request, $result);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->ErrorResponse($th->getMessage());
        }

        $this->afterStore($request, $result);

        return $this->SuccessResponse(['id' => $result->id], 0, Messages::CREATED_SUCCESS);
    }

    public function update(Request $request, $id)
    {
        app($this->request);

        $modelResult = $this->model->findOrFail($id);

        DB::beginTransaction();
        try {
            $data = $this->beforeUpdate($request, $modelResult);

            $result = $modelResult->update($data);

            $this->afterUpdate($request, $modelResult, $result);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->ErrorResponse($th->getMessage());
        }

        return $this->SuccessResponse([], 0, Messages::UPDATE_SUCCESS);
    }

    public function destroy($id)
    {
        $modelResult = $this->model->findOrFail($id);

        try {
            $this->beforeDestroy($modelResult);

            $this->model->destroy($id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->ErrorResponse(Messages::ERROR_OBJECT_USED_BEFORE);
        }

        return $this->SuccessResponse(['id' => $id], 0, Messages::DELETE_SUCCESS);
    }


    /**
     * The hook is executed before creating new resource.
     *
     * @param  Model  $entity
     * @return mixed
     */
    protected function beforeStore(Request $request)
    {
        return array_merge($request->all(), $request->all()['translations'] ?? []);
    }

    /**
     * The hook is executed after creating new resource.
     *
     * @param  Model  $entity
     * @return mixed
     */
    protected function afterStore(Request $request, $result)
    {
        return null;
    }

    /**
     * The hook is executed after creating new resource.
     *
     * @param  Model  $entity
     * @return mixed
     */
    protected function afterSave(Request $request, $result)
    {
        return null;
    }

    /**
     * The hook is executed before update.
     *
     * @return mixed
     */
    protected function beforeUpdate(Request $request, $old_model)
    {
        return array_merge($request->all(), $request->all()['translations'] ?? []);
    }

    /**
     * The hook is executed after update resource.
     *
     * @return mixed
     */
    protected function afterUpdate(Request $request, $model, $result)
    {
        return null;
    }

    /**
     * The hook is executed before destroy.
     *
     * @return mixed
     */
    protected function beforeDestroy($item)
    {
        return null;
    }
}
