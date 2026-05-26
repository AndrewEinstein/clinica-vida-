<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\InsuranceProvider;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

abstract class BaseCrudController extends Controller
{
    protected string $modelClass;

    protected string $routeName;

    protected string $viewPrefix;

    protected string $title;

    protected string $singularTitle;

    protected array $with = [];

    protected array $searchable = [];

    protected ?string $rowActionsView = null;

    protected string $orderBy = 'created_at';

    protected string $orderDirection = 'desc';

    public function index(Request $request): View
    {
        $this->authorize('viewAny', $this->modelClass);

        $query = $this->baseQuery();
        $this->applySearch($query, $request);
        $this->applyFilters($query, $request);

        $items = $query
            ->orderBy($this->orderBy, $this->orderDirection)
            ->paginate(10)
            ->withQueryString();

        return view($this->viewPrefix.'.index', [
            'title' => $this->title,
            'singularTitle' => $this->singularTitle,
            'routeName' => $this->routeName,
            'modelClass' => $this->modelClass,
            'items' => $items,
            'columns' => $this->columns(),
            'filters' => $this->filters(),
            'rowActionsView' => $this->rowActionsView,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', $this->modelClass);

        $record = new $this->modelClass;

        return view($this->viewPrefix.'.form', [
            'title' => 'Novo '.$this->singularTitle,
            'routeName' => $this->routeName,
            'record' => $record,
            'fields' => $this->fields($record),
            'method' => 'POST',
            'action' => route($this->routeName.'.store'),
        ]);
    }

    public function show(string $id): View
    {
        $record = $this->findRecord($id);
        $this->authorize('view', $record);

        return view($this->viewPrefix.'.show', [
            'title' => $this->singularTitle,
            'routeName' => $this->routeName,
            'record' => $record,
            'fields' => $this->showFields($record),
        ]);
    }

    public function edit(string $id): View
    {
        $record = $this->findRecord($id);
        $this->authorize('update', $record);

        return view($this->viewPrefix.'.form', [
            'title' => 'Editar '.$this->singularTitle,
            'routeName' => $this->routeName,
            'record' => $record,
            'fields' => $this->fields($record),
            'method' => 'PUT',
            'action' => route($this->routeName.'.update', $record),
        ]);
    }

    public function destroy(string $id): RedirectResponse
    {
        $record = $this->findRecord($id);
        $this->authorize('delete', $record);
        $record->delete();

        return redirect()
            ->route($this->routeName.'.index')
            ->with('success', $this->singularTitle.' removido com sucesso.');
    }

    protected function storeRecord(FormRequest $request): RedirectResponse
    {
        $this->authorize('create', $this->modelClass);

        $record = $this->modelClass::create($this->prepareData($request->validated()));
        $this->afterSave($record, $request);

        return redirect()
            ->route($this->routeName.'.show', $record)
            ->with('success', $this->singularTitle.' cadastrado com sucesso.');
    }

    protected function updateRecord(FormRequest $request, Model $record): RedirectResponse
    {
        $this->authorize('update', $record);

        $record->update($this->prepareData($request->validated(), $record));
        $this->afterSave($record, $request);

        return redirect()
            ->route($this->routeName.'.show', $record)
            ->with('success', $this->singularTitle.' atualizado com sucesso.');
    }

    protected function baseQuery(): Builder
    {
        $query = $this->modelClass::query()->with($this->with);

        // Enforce multi-clinic data separation for all clinic-scoped tables.
        // If the model has a clinic_id column and the user is not Super Admin,
        // restrict all reads to the user's clinic.
        if (auth()->check() && ! auth()->user()->isSuperAdmin()) {
            $model = new $this->modelClass;
            $table = $model->getTable();

            if (Schema::hasColumn($table, 'clinic_id')) {
                $query->where($table.'.clinic_id', auth()->user()->clinic_id);
            }
        }

        return $query;
    }

    protected function findRecord(string $id): Model
    {
        return $this->baseQuery()->findOrFail($id);
    }

    protected function applySearch(Builder $query, Request $request): void
    {
        $term = trim((string) $request->query('q'));

        if ($term === '' || $this->searchable === []) {
            return;
        }

        $query->where(function (Builder $subQuery) use ($term): void {
            foreach ($this->searchable as $column) {
                $subQuery->orWhere($column, 'like', '%'.$term.'%');
            }
        });
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        foreach ($this->filters() as $filter) {
            $name = $filter['name'];

            if (! $request->filled($name)) {
                continue;
            }

            $column = $filter['column'] ?? $name;
            $value = $request->query($name);

            match ($filter['operator'] ?? '=') {
                'date>=' => $query->whereDate($column, '>=', $value),
                'date<=' => $query->whereDate($column, '<=', $value),
                default => $query->where($column, $value),
            };
        }
    }

    protected function prepareData(array $data, ?Model $record = null): array
    {
        if (auth()->check() && ! auth()->user()->isSuperAdmin()) {
            $data['clinic_id'] = auth()->user()->clinic_id;
        }

        return $data;
    }

    protected function afterSave(Model $record, FormRequest $request): void
    {
        //
    }

    protected function filters(): array
    {
        return [];
    }

    protected function showFields(Model $record): array
    {
        return array_values(array_filter($this->fields($record), fn (array $field): bool => ($field['type'] ?? null) !== 'password'));
    }

    protected function clinicOptions(): array
    {
        return Clinic::query()->orderBy('name')->pluck('name', 'id')->toArray();
    }

    protected function userOptions(array $roles = []): array
    {
        $query = User::query()->orderBy('name');

        if (! auth()->user()->isSuperAdmin()) {
            $query->where('clinic_id', auth()->user()->clinic_id);
        }

        if ($roles !== []) {
            $query->whereIn('role', $roles);
        }

        return $query->pluck('name', 'id')->toArray();
    }

    protected function patientOptions(): array
    {
        return Patient::query()->orderBy('name')->pluck('name', 'id')->toArray();
    }

    protected function doctorOptions(): array
    {
        return Doctor::query()->orderBy('name')->pluck('name', 'id')->toArray();
    }

    protected function insuranceOptions(): array
    {
        return InsuranceProvider::query()->orderBy('name')->pluck('name', 'id')->toArray();
    }

    protected function appointmentOptions(): array
    {
        return Appointment::query()
            ->with(['patient', 'doctor'])
            ->orderByDesc('scheduled_at')
            ->limit(100)
            ->get()
            ->mapWithKeys(fn (Appointment $appointment): array => [
                $appointment->id => '#'.$appointment->id.' - '.$appointment->patient?->name.' com '.$appointment->doctor?->name.' em '.$appointment->scheduled_at?->format('d/m/Y H:i'),
            ])
            ->toArray();
    }

    abstract protected function columns(): array;

    abstract protected function fields(?Model $record = null): array;
}
