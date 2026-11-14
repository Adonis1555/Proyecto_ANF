
@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-slate-100 px-6 py-14">

    <div class="mx-auto max-w-5xl">

        {{-- ENCABEZADO --}}
        <div class="mb-8">

            <h1 class="text-2xl font-bold text-slate-900">
                Seleccionar cuenta base
            </h1>

            <p class="mt-3 text-sm leading-6 text-slate-500">
                EconomyManager no pudo identificar automáticamente
                una base única para el análisis vertical.
                Selecciona la cuenta que representa el total de
                referencia de este estado financiero.
            </p>

        </div>

        {{-- TARJETA PRINCIPAL --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">

            {{-- INFORMACIÓN DEL REPORTE --}}
            <div class="mb-7 flex flex-wrap items-center gap-3">

                <span class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700">
                    {{ $tipoReporte === 'balance'
                        ? 'Balance General'
                        : 'Estado de Resultados' }}
                </span>

                <span class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600">
                    Año {{ $anio }}
                </span>

            </div>

            <form method="GET"
                  action="{{ route('analisis.vertical') }}">

                <input type="hidden"
                       name="empresa_id"
                       value="{{ $empresaId }}">

                <input type="hidden"
                       name="tipo_reporte"
                       value="{{ $tipoReporte }}">

                <input type="hidden"
                       name="anio"
                       value="{{ $anio }}">

                {{-- SELECCIÓN --}}
                <div class="mb-7">

                    <label for="cuenta_base_id"
                           class="mb-3 block text-sm font-semibold text-slate-800">
                        Cuenta base del análisis
                    </label>

                    <select
                        name="cuenta_base_id"
                        id="cuenta_base_id"
                        required
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">

                        <option value="">
                            Selecciona una cuenta
                        </option>

                        @foreach($candidatas as $cuenta)
                            <option value="{{ $cuenta->id }}"
                                {{ old('cuenta_base_id') == $cuenta->id ? 'selected' : '' }}>
                                {{ $cuenta->codigo }} -
                                {{ $cuenta->nombre }}
                                (${{ number_format($cuenta->saldo, 2) }})
                            </option>
                        @endforeach

                    </select>

                    @error('cuenta_base_id')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="mt-3 text-xs leading-5 text-slate-500">
                        La cuenta seleccionada representará el 100 %
                        para calcular los porcentajes del análisis.
                    </p>

                </div>

                {{-- ACCIONES --}}
                <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-100 pt-6 sm:flex-row">

                    <a href="{{ route('analisis.historial.vertical') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-6 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">

                        <i class="bi bi-calculator"></i>
                        Generar análisis vertical

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection
