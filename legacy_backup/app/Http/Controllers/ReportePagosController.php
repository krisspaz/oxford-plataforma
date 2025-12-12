<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Convenio;
use App\Models\Cuota;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PagosFiltradosExport;

class ReportePagosController extends Controller
{


    public function index(Request $request)
    {
        $query = Pago::with('convenio');

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_pago', $request->fecha);
        }

        if ($request->filled('tipo_pago')) {
            $query->where('tipo_pago', 'like', '%' . $request->tipo_pago . '%');
        }

        $totalFiltrado = $query->sum('monto');
        $pagos = $query->orderBy('fecha_pago', 'desc')->get();

        return view('reporte_pagos.index', compact('pagos', 'totalFiltrado'));
    }





    public function create()
    {
        $convenios = Convenio::all();
        $cuotas = Cuota::all();
        return view('reporte_pagos.create', compact('convenios', 'cuotas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'convenio_id' => 'required|exists:convenios,id',
            'tipo_pago' => 'required',
            'monto' => 'required|numeric',
            'fecha_pago' => 'required|date',
            'cuotas' => 'array',
        ]);

        $pago = Pago::create($request->only(['convenio_id', 'tipo_pago', 'monto', 'exonerar', 'fecha_pago']));

        // Relacionar cuotas si vienen en el request
        if ($request->has('cuotas')) {
            $pago->cuotas()->sync($request->cuotas);
        }

        return redirect()->route('reporte-pagos.index')->with('success', 'Pago creado correctamente');
    }

    public function show(Pago $reporte_pago)
    {
        return view('reporte_pagos.show', compact('reporte_pago'));
    }

    public function edit(Pago $reporte_pago)
    {
        $convenios = Convenio::where('id', $reporte_pago->convenio_id)->get();
        $cuotas = Cuota::where('convenio_id', $reporte_pago->convenio_id)->get();
        return view('reporte_pagos.edit', compact('reporte_pago', 'convenios', 'cuotas'));
    }

    public function update(Request $request, Pago $reporte_pago)
    {
        $request->validate([
            'convenio_id' => 'required|exists:convenios,id',
            'tipo_pago' => 'required',
            'monto' => 'required|numeric',
            'fecha_pago' => 'required|date',
            'cuotas' => 'array',
        ]);

        try {
            $reporte_pago->update($request->only([
                'convenio_id', 'tipo_pago', 'monto', 'exonerar', 'fecha_pago'
            ]));

            if ($request->has('cuotas')) {
                $reporte_pago->cuotas()->sync($request->cuotas);
            }

            return redirect()->route('reporte_pagos.index')->with('success', 'Pago actualizado correctamente');


        } catch (QueryException $e) {
            // Detectar si es el trigger que devuelve error con código 1644
            $mensaje = $e->getMessage();

            if ($e->getCode() == '45000') {
                if (preg_match('/1644 (.+?)\./', $mensaje, $coincide)) {
                    $mensaje = $coincide[1];
                }
            }

            return redirect()->back()->with('error', $mensaje);
        }
    }

    public function destroy(Pago $reporte_pago)
    {

        $reporte_pago->delete();

        return redirect()->route('reporte_pagos.index')->with('success', 'Pago eliminado correctamente');
    }


    public function exportExcel(Request $request)
    {
        $query = Pago::query();

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_pago', $request->fecha);
        }

        if ($request->filled('tipo_pago')) {
            $query->where('tipo_pago', 'like', '%' . $request->tipo_pago . '%');
        }

        $pagos = $query->get();

        return Excel::download(new PagosFiltradosExport($pagos), 'reporte_pagos.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = Pago::query();

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_pago', $request->fecha);
        }

        if ($request->filled('tipo_pago')) {
            $query->where('tipo_pago', 'like', '%' . $request->tipo_pago . '%');
        }

        $pagos = $query->get();
        $totalFiltrado = $query->sum('monto');

        $pdf = \PDF::loadView('reporte_pagos.export_pdf', compact('pagos', 'totalFiltrado'));
        return $pdf->download('reporte_pagos.pdf');
    }
}
