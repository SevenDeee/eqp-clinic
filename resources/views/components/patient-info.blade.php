<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Prescription - Karli Ziemann DDS</title>
    <style>
        body {
            background: #fde8e8;
            font-family: Arial, Helvetica, sans-serif;
            color: #21262e;
            margin: -20;
            padding: 10;
        }

        .container {
            max-width: 968px;
            margin: 0 0;
            background: #fff;
            border-radius: 1px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            padding: 2em;
            padding-bottom: 2;
        }

        h1,
        h2 {
            margin: 0 0 0.5em 0;
        }

        h1 {
            font-size: 1.5em;
            font-weight: bold;
        }

        h2 {
            font-size: 1.1em;
            font-weight: bold;
            border-bottom: 1px solid #040404;
            padding-bottom: 10;
            margin-bottom: 10;
        }

        p {
            margin: 0.4em 0;
            line-height: 1.4em;
        }

        .font-semibold {
            font-weight: bold;
        }

        .text-sm {
            font-size: 0.9em;
            color: #718096;
        }

        .text-gray-500 {
            color: #718096;
        }

        .text-gray-600 {
            color: #4a5568;
        }

        .text-gray-700 {
            color: #2d3748;
        }

        .text-center {
            text-align: center;
        }

        .italic {
            font-style: italic;
        }

        header {
            border-bottom: 1px solid #010101;
            margin-bottom: 8;
            padding-bottom: 1;
        }

        header .left {
            float: left;
            width: 60%;
        }

        header .right {
            float: right;
            width: 45%;
            text-align: right;
        }

        header:after {
            content: "";
            display: block;
            clear: both;
        }

        section {
            margin-bottom: 1.5em;
        }

        .columns {
            /* overflow: hidden; */
        }

        .col {
            float: left;
            width: 48%;
            margin-right: 4%;
        }

        .col:last-child {
            margin-right: 0;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 0.9em;
            text-align: center;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 0.5em 0.8em;
        }

        th {
            background: #f9fafb;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        footer {
            border-top: 1px solid #050505;
            margin-top: 180px;
            font-size: 0.9em;
            color: #4a5568;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="left">
                <h1>Patient Information</h1>
                <p class="text-sm text-gray-500">Prescribed On {{ $prescription->created_at->format('F j, Y') }}</p>
            </div>
            <div class="right">
                <p class="font-semibold">Follow-up: <span
                        class="text-gray-700">{{ $patient->follow_up_on ? \Carbon\Carbon::parse($patient->follow_up_on)->format('F j, Y') : 'N/A' }}</span>
                </p>
                <p class="text-sm text-gray-600" style="padding-right: 00px">Prescribed by: Dr.
                    {{ $prescription->prescriber->name }}</p>
            </div>
        </header>

        <section>
            <h2>Patient Information</h2>
            <div class="columns">
                <div class="col">
                    <p><span class="font-semibold">Name:</span> {{ $patient->name }}</p>
                    <p><span class="font-semibold">Age:</span> {{ $patient->age }}</p>
                    <p><span class="font-semibold">Sex:</span> {{ $patient->sex }}</p>
                    <p><span class="font-semibold">Contact:</span> {{ $patient->contact_number }}</p>
                </div>
                <div class="col">
                    <p><span class="font-semibold">Address:</span><br>
                        {{ $patient->address }}</p>
                </div>
            </div>
        </section>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>

        <section>
            <h2>Rx:</h2>
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Eye</th>
                        <th>Sphere</th>
                        <th>Cylinder</th>
                        <th>Axis</th>
                        <th>Mono PD</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (['far', 'near'] as $type)
                        <tr>
                            <td rowspan="2" class="font-semibold text-capitalize">{{ ucfirst($type) }}</td>
                            <td>OD (Right)</td>
                            <td>{{ $prescription[$type]['od']['sphere'] ?? '' }}</td>
                            <td>{{ $prescription[$type]['od']['cylinder'] ?? '' }}</td>
                            <td>{{ $prescription[$type]['od']['axis'] ?? '' }}</td>
                            <td>{{ $prescription[$type]['od']['monopd'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>OS (Left)</td>
                            <td>{{ $prescription[$type]['os']['sphere'] ?? '' }}</td>
                            <td>{{ $prescription[$type]['os']['cylinder'] ?? '' }}</td>
                            <td>{{ $prescription[$type]['os']['axis'] ?? '' }}</td>
                            <td>{{ $prescription[$type]['os']['monopd'] ?? '' }}</td>
                        </tr>
                    @endforeach
                    <td colspan="2" class="font-semibold">Remark(s)</td>
                    <td colspan="4" class="font-semibold">{{ $prescription->remarks }}</td>

                </tbody>
            </table>
        </section>

        <section>
            <h2>Additional Information</h2>
            <div class="columns">
                <div class="col">
                    <p><span class="font-semibold">Frame Type:</span> {{ $patient->frame_type }}</p>
                    <p><span class="font-semibold">Color:</span> {{ $patient->color }}</p>
                    <p><span class="font-semibold">Special Instructions:</span> {{ $patient->special_instructions }}
                    </p>
                    <p><span class="font-semibold">Lens Supply:</span>
                        {{ $patient->lens_suplly ? $patient->lens_suplly : 'N/A' }}</p>
                    <p><span class="font-semibold">Diagnosis:</span> {{ $patient->diagnosis }}</p>
                </div>
                <div class="col">
                    <p><span class="font-semibold">Amount:</span> P{{ number_format($patient->amount) }}</p>
                    <p><span class="font-semibold">Deposit:</span> P{{ number_format($patient->deposit) }}</p>
                    <p><span class="font-semibold">Balance:</span> P{{ number_format($patient->balance) }}</p>
                </div>
            </div>
        </section>

        <footer>
            <p>Thank you for trusting our clinic.</p>
            <p class="italic">© 2025 EQP Optical Clinic</p>
        </footer>
        <br>
        <br>
        <p class="italic text-gray-500" style="padding-left: 10; padding-bottom: 10; text-align: right; font-size: 10">
            Issued At: {{ now()->format('F d, Y') }}</p>
    </div>
</body>

</html>
