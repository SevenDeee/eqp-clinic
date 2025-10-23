<div class="bg-white shadow rounded-lg p-6 pb-5 mb-8 border border-gray-200">
    <div class="flex justify-between items-center mb-0">
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Rx:</h2>
        <div class="text-sm text-gray-500 mb-2 flex items-center">
            <label class="mr-2">Prescribed On:</label>
            <select wire:model.live="prescribedDate"
                class="border border-gray-300 rounded-md px-2 py-1 text-gray-700 focus:ring-1 focus:ring-blue-400 focus:outline-none">
                @foreach ($record->prescriptions->sortByDesc('created_at') as $prescription)
                    <option value="{{ $prescription->id }}">
                        {{ $prescription->created_at->format('M d, Y') }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    @foreach ($prescriptions as $prescription)
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-400 text-sm text-gray-800">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-400 px-3 py-2 text-left w-20"> </th>
                        <th class="border border-gray-400 px-3 py-2 text-left w-12"> </th>
                        <th class="border border-gray-400 px-3 py-2 text-center">SPHERE</th>
                        <th class="border border-gray-400 px-3 py-2 text-center">CYLINDER</th>
                        <th class="border border-gray-400 px-3 py-2 text-center">AXIS</th>
                        <th class="border border-gray-400 px-3 py-2 text-center">MonoPD</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-400 px-3 py-2 align-middle font-semibold bg-gray-50"
                            rowspan="2">FAR</td>
                        <td class="border border-gray-400 px-3 py-2 font-semibold">OD</td>
                        <td class="border border-gray-400 px-3 py-2 text-center">
                            {{ $prescription->far['od']['sphere'] }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-center">
                            {{ $prescription->far['od']['cylinder'] }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-center">{{ $prescription->far['od']['axis'] }}
                        </td>
                        <td class="border border-gray-400 px-3 py-2 text-center">
                            {{ $prescription->far['od']['monopd'] }}</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-400 px-3 py-2 font-semibold">OS</td>
                        <td class="border border-gray-400 px-3 py-2 text-center">
                            {{ $prescription->far['os']['sphere'] }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-center">
                            {{ $prescription->far['os']['cylinder'] }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-center">{{ $prescription->far['os']['axis'] }}
                        </td>
                        <td class="border border-gray-400 px-3 py-2 text-center">
                            {{ $prescription->far['os']['monopd'] }}</td>
                    </tr>

                    <tr>
                        <td class="border border-gray-400 px-3 py-2 align-middle font-semibold bg-gray-50"
                            rowspan="2">NEAR</td>
                        <td class="border border-gray-400 px-3 py-2 font-semibold">OD</td>
                        <td class="border border-gray-400 px-3 py-2 text-center">
                            {{ $prescription->near['od']['sphere'] }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-center">
                            {{ $prescription->near['od']['cylinder'] }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-center">
                            {{ $prescription->near['od']['axis'] }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-center">
                            {{ $prescription->near['od']['monopd'] }}</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-400 px-3 py-2 font-semibold">OS</td>
                        <td class="border border-gray-400 px-3 py-2 text-center">
                            {{ $prescription->near['os']['sphere'] }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-center">
                            {{ $prescription->near['os']['cylinder'] }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-center">
                            {{ $prescription->near['os']['axis'] }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-center">
                            {{ $prescription->near['os']['monopd'] }}</td>
                    </tr>

                    <tr>
                        <td class="border border-gray-400 px-3 py-2 text-right font-semibold" colspan="2">Remark(s):
                        </td>
                        <td class="border border-gray-400 px-3 py-2 text-left" colspan="4">
                            {{ $prescription->remarks }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="text-sm text-gray-500 mt-1 text-right">
            Prescribed By Dr. {{ $prescription->prescriber->name }}
        </div>
    @endforeach
</div>
