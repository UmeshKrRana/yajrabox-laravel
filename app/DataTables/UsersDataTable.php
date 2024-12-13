<?php

namespace App\DataTables;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function() {
                return '
                    <a href="" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>
                    <a href="" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
                ';
            })
            ->editColumn('created_at', function($user) {
                return Carbon::parse($user->created_at)->format('d-m-Y');
            })
            ->editColumn('updated_at', function($user) {
                return Carbon::parse($user->updated_at)->format('d-m-Y');
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        $startDate = request('startDate') ?? null;
        $endDate = request('endDate') ?? null;

        $query = $model->newQuery();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('users-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax('', null, [
                        'startDate' => "$('#start-date').val();",
                        'endDate' => "$('#end-date').val();",
                    ])
                    // ->ajax()
                    ->parameters([
                        'order' => [[0, 'asc']],
                        'dom' => '<"top mb-2"Bfl>rt<"bottom d-flex align-items-center justify-content-between mt-3"ip>',
                        'initComplete' => 'function() {
                            const table = this.api();

                            const $thead = $(table.table().header());

                            const $filterRow = $thead.find("tr").clone().addClass("filter");

                            $filterRow.find("th").each(function() {
                                const $currentTh = $(this);

                                if (!$currentTh.hasClass("no-search")) {

                                    const input = $(`<input type="text" class="form-control form-control-sm" placeholder="Search ${$currentTh.text()}" /> `);
                                    $currentTh.html(input);

                                    $(input).on("click", function(event) {
                                        event.stopPropagation();
                                    });

                                    $(input).on("keyup change clear", function() {
                                        if (table.column($currentTh.index()).search() !== this.value) {
                                            table.column($currentTh.index()).search(this.value).draw();
                                        }
                                    });

                                } else {
                                    $currentTh.empty();
                                }

                            });

                            $thead.append($filterRow);


                            // for the date filter
                            $("#start-date, #end-date").on("change", function() {
                                const startDate = $("#start-date").val();
                                const endDate = $("#end-date").val();

                                if (startDate && endDate) {
                                    table.ajax.reload();
                                }
                            });

                        }'
                    ])
                    // ->dom('Bfrtip')
                    ->orderBy(0)
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('excel'),
                        Button::make('csv'),
                        Button::make('pdf'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->width(120),
            Column::make('name'),
            Column::make('email'),
            Column::make('created_at'),
            Column::make('updated_at'),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(100)
                  ->addClass('text-center no-search'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
