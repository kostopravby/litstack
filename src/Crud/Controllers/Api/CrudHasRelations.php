<?php

namespace Fjord\Crud\Controllers\Api;

use Fjord\Support\IndexTable;
use Fjord\Crud\Fields\Blocks\Blocks;
use Fjord\Crud\Fields\Relations\HasOne;
use Fjord\Crud\Fields\Relations\HasMany;
use Fjord\Crud\Fields\Relations\MorphTo;
use Fjord\Crud\Requests\CrudReadRequest;
use Fjord\Crud\Fields\Relations\MorphOne;
use Fjord\Crud\Fields\Relations\MorphMany;
use Fjord\Crud\Requests\CrudUpdateRequest;
use Fjord\Crud\Fields\Relations\MorphToMany;
use Fjord\Crud\Fields\Relations\OneRelation;
use Fjord\Crud\Fields\Relations\ManyRelation;
use Fjord\Crud\Fields\Relations\BelongsToMany;
use Fjord\Crud\Fields\Relations\OneRelationField;
use Fjord\Crud\Fields\Relations\LaravelRelationField;

trait CrudHasRelations
{
    use Relations\ManagesBelongsToMany,
        Relations\ManagesManyRelation,
        Relations\ManagesMorphOne,
        Relations\ManagesMorphToMany,
        Relations\ManagesOneRelation,
        Relations\ManagesHasOne,
        Relations\ManagesHasMany,
        Relations\ManagesMorphMany,
        Relations\ManagesMorphTo;

    /**
     * Load relation index.
     *
     * @param CrudReadRequest $request
     * @param int $id
     * @param string $relation
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function relationIndex(CrudReadRequest $request, $id, $relation)
    {
        $model = $this->query()->findOrFail($id);
        $field = $this->config->form->findField($relation) ?? abort(404);

        $this->validateRelationField($field);

        $index = IndexTable::query($field->getQuery())
            ->request($request)
            ->search($field->getRelatedConfig()->search)
            ->get();

        $index['items'] = crud($index['items']);

        return $index;
    }

    /**
     * Fetch existing relations.
     *
     * @param CrudReadRequest $request
     * @param int $id
     * @param int $field_id
     * @return void
     */
    public function loadRelations(CrudReadRequest $request, $id, $relation)
    {
        $model = $this->query()->findOrFail($id);
        $field = $this->config->form->findField($relation) ?? abort(404);

        $this->validateRelationField($field);

        $query = $field->getRelationQuery($model, $query = true);

        if ($field instanceof OneRelationField) {
            $items = $query->get();
            $relations = collect([
                'count' => $items->count(),
                'items' => $items
            ]);
        } else {
            $relations = IndexTable::query($query)
                ->request($request)
                ->search($field->getRelatedConfig()->search)
                ->only(['filter', 'paginate'])
                ->get();
        }

        $relations['items'] = crud(
            $relations['items']
        );

        return $relations;
    }

    /**
     * Add new relation.
     *
     * @param CrudUpdateRequest $request
     * @param int $id
     * @param string $relation
     * @param int $relation_id
     * @return mixed
     */
    public function createRelation(CrudUpdateRequest $request, $id, $relation, $relation_id)
    {
        $model = $this->query()->findOrFail($id);
        $field = $this->config->form->findField($relation) ?? abort(404);

        $this->validateRelationField($field);

        $relation = $field->getQuery()->findOrFail($relation_id);

        $this->createFieldRelation($request, $field, $model, $relation);

        $this->edited($model, 'relation:created');

        return crud($relation);
    }

    /**
     * Create Field relation.
     *
     * @param CrudUpdateRequest $request
     * @param mixed $field
     * @param mixed $model
     * @param mixed $relation
     * @return void
     */
    public function createFieldRelation(CrudUpdateRequest $request, $field, $model, $relation)
    {
        if ($field instanceof OneRelation) {
            return $this->createOneRelation($request, $field, $model, $relation);
        }
        if ($field instanceof ManyRelation) {
            return $this->createManyRelation($request, $field, $model, $relation);
        }
        if ($field instanceof BelongsToMany) {
            return $this->createBelongsToMany($request, $field, $model, $relation);
        }
        if ($field instanceof MorphToMany) {
            return $this->createMorphToMany($request, $field, $model, $relation);
        }
        if ($field instanceof MorphOne) {
            return $this->createMorphOne($request, $field, $model, $relation);
        }
        if ($field instanceof MorphTo) {
            return $this->createMorphTo($request, $field, $model, $relation);
        }
        if ($field instanceof HasOne) {
            return $this->createHasOne($request, $field, $model, $relation);
        }
        if ($field instanceof HasMany) {
            return $this->createHasMany($request, $field, $model, $relation);
        }
        if ($field instanceof MorphMany) {
            return $this->createMorphMany($request, $field, $model, $relation);
        }

        abort(405);
    }

    /**
     * Remove relation.
     *
     * @param CrudUpdateRequest $request
     * @param int $id
     * @param string $relation
     * @param int $relation_id
     * @return mixed
     */
    public function destroyRelation(CrudUpdateRequest $request, $id, $relation, $relation_id)
    {
        $model = $this->query()->findOrFail($id);
        $field = $this->config->form->findField($relation) ?? abort(404);

        $this->validateRelationField($field);

        $relation = $field->getQuery()->findOrFail($relation_id);

        $response = $this->destroyFieldRelation($request, $field, $model, $relation);

        $this->edited($model, 'relation:deleted');

        return $response;
    }

    /**
     * Destroy field relation.
     *
     * @param CrudUpdateRequest $request
     * @param mixed $field
     * @param mixed $model
     * @param mixed $relation
     * @return void
     */
    protected function destroyFieldRelation(CrudUpdateRequest $request, $field, $model, $relation)
    {
        if ($field instanceof OneRelation) {
            return $this->destroyOneRelation($request, $field, $model, $relation);
        }
        if ($field instanceof ManyRelation) {
            return $this->destroyManyRelation($request, $field, $model, $relation);
        }
        if ($field instanceof BelongsToMany) {
            return $this->destroyBelongsToMany($request, $field, $model, $relation);
        }
        if ($field instanceof MorphToMany) {
            return $this->destroyMorphToMany($request, $field, $model, $relation);
        }
        if ($field instanceof MorphOne) {
            return $this->destroyMorphOne($request, $field, $model, $relation);
        }
        if ($field instanceof MorphTo) {
            return $this->destroyMorphTo($request, $field, $model, $relation);
        }
        if ($field instanceof HasOne) {
            return $this->destroyHasOne($request, $field, $model, $relation);
        }
        if ($field instanceof HasMany) {
            return $this->destroyHasMany($request, $field, $model, $relation);
        }
        if ($field instanceof MorphMany) {
            return $this->destroyMorphMany($request, $field, $model, $relation);
        }

        abort(405);
    }

    /**
     * Order relations.
     *
     * @param CrudUpdateRequest $request
     * @param int $id
     * @param string $relation
     * @return void
     */
    public function orderRelation(CrudUpdateRequest $request, $id, $relation)
    {

        $ids = $request->ids ?? abort(404);
        $model = $this->query()->findOrFail($id);
        $field = $this->config->form->findField($relation) ?? abort(404);

        $query = $field->getRelationQuery($model, $query = true);

        $order = $this->orderField($query, $field, $ids);

        $this->edited($model, 'relation:ordered');

        return $order;
    }

    /**
     * Is the field a valid relation field
     *
     * @param mixed $field
     * @return boolean
     */
    protected function validateRelationField($field)
    {
        if (!$field instanceof LaravelRelationField) {
            abort(404);
        }

        if ($field instanceof Blocks) {
            abort(404);
        }

        return true;
    }
}
