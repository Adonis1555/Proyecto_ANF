
@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">
            Seleccionar cuenta base
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            EconomyManager no pudo identificar automáticamente
            una base única para el análisis vertical.
            Selecciona la cuenta que representa el total
            de referencia de este estado financiero.
        </p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <form method="GET" action="{{ route('analisis.vertical') }}">

            <input type="hidden"
                   name="empresa_id"
                   value="{{ $empresaId }}">

            <input type="hidden"
                   name="tipo_reporte"
                   value="{{ $tipoReporte }}">

            <input type="hidden"
                   name="anio"
                   value="{{ $anio }}">

            <label for="cuenta_base_id"
                   class="mb-2 block text-sm font-semibold text-slate-700">
                Cuenta base del análisis
            </label>

            <select
                name="cuenta_base_id"
                id="cuenta_base_id"
                required
                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none"
            >
                <option value="">
                    Selecciona una cuenta
                </option>

                @foreach($candidatas as $cuenta)
                    <option value="{{ $cuenta->id }}">
                        {{ $cuenta->codigo }} -
                        {{ $cuenta->nombre }}
                        ({{ number_format($cuenta->saldo, 2) }})
                    </option>
                @endforeach
            </select>

            @error('cuenta_base_id')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror

            <div class="mt-6 flex justify-end gap-3">

                <a href="{{ route('analisis.historial.vertical') }}"
                   class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700">
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    Generar análisis vertical
                </button>

            </div>
        </form>

    </div>
</div>
@endsection
