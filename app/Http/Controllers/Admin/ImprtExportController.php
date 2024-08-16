<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExcelData;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImprtExportController extends Controller
{
    public function import_export(Request $request) {
        $query = ExcelData::query();
    // dd($request->all());
        // Filter by start date and end date if both are provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('Date', [$request->input('start_date'), $request->input('end_date')]);
        }
        // Filter by start date if provided
        elseif ($request->filled('start_date')) {
            $query->whereDate('Date', '>=', $request->input('start_date'));
        }
        // Filter by end date if provided
        elseif ($request->filled('end_date')) {
            $query->whereDate('Date', '<=', $request->input('end_date'));
        }
    
        // Get filtered data or all data if no filter is applied
       
    // Get filtered data with pagination
          $datas = $query->paginate(10); // Change 10 to the number of items per page you want
    
        return view('admin.import_export', compact('datas'));
    }
    
    public function import_file(Request $request)
    {
        try {
        // Validate the file upload
        // $request->validate([
        //     'import_file' => 'required|mimes:csv,txt',
        // ]);

        // Retrieve the uploaded file
        $file = $request->file('import_file');
        $csvData = file_get_contents($file);

        // Split the file content into lines
        $lines = explode("\n", $csvData);

        // Remove the header line
        $header = str_getcsv(array_shift($lines));

        $ExcelDatas = []; // Initialize the array to collect data

        foreach ($lines as $line) {
            $line = trim($line); // Trim any extra whitespace
            if (empty($line)) continue; // Skip empty lines

            $row = str_getcsv($line);

            // Remove the first column
            array_shift($row);

            // Add each row to the array, ensuring correct mapping
            $formattedDate = Carbon::createFromFormat('m/d/Y', $row[0])->format('Y-m-d');
            $ExcelDatas[] = [
                'Date' => $formattedDate ?? null,
                // 'Time_or_Hour' => $row[1] ?? null,
                'Time_or_Hour' => isset($row[1]) ? Carbon::parse($row[1])->format('H:i') : null,
                'GenerationUnit1_NozelOpen' => $row[2] ?? null,
                'GenerationUnit1_GeneratorVoltage_AY' => $row[3] ?? null,
                'GenerationUnit1_GeneratorVoltage_YB' => $row[4] ?? null,
                'GenerationUnit1_GeneratorVoltage_BR' => $row[5] ?? null,
                'GenerationUnit1_GeneratorCurrent_I1' => $row[6] ?? null,
                'GenerationUnit1_GeneratorCurrent_I2' => $row[7] ?? null,
                'GenerationUnit1_GeneratorCurrent_I3' => $row[8] ?? null,
                'GenerationUnit1_GeneratorOutput_KW' => $row[9] ?? null,
                'GenerationUnit1_GeneratorOutput_kVAr' => $row[10] ?? null,
                'GenerationUnit1_PF_Close' => $row[11] ?? null,
                'GenerationUnit1_Frequency_HZ' => $row[12] ?? null,
                'GenerationUnit1_Energy_kWH' => $row[13] ?? null,
                'GenerationUnit2_NozelOpen' => $row[14] ?? null,
                'GenerationUnit2_GeneratorVoltage_AY' => $row[15] ?? null,
                'GenerationUnit2_GeneratorVoltage_YB' => $row[16] ?? null,
                'GenerationUnit2_GeneratorVoltage_BR' => $row[17] ?? null,
                'GenerationUnit2_GeneratorCurrent_I1' => $row[18] ?? null,
                'GenerationUnit2_GeneratorCurrent_I2' => $row[19] ?? null,
                'GenerationUnit2_GeneratorCurrent_I3' => $row[20] ?? null,
                'GenerationUnit2_GeneratorOutput_KW' => $row[21] ?? null,
                'GenerationUnit2_GeneratorOutput_kVAr' => $row[22] ?? null,
                'GenerationUnit2_PF_Close' => $row[23] ?? null,
                'GenerationUnit2_Frequency_HZ' => $row[24] ?? null,
                'GenerationUnit2_Energy_kWH' => $row[25] ?? null,
                '_11KVSide_LineVoltage_RY' => $row[26] ?? null,
                '_11KVSide_LineVoltage_YB' => $row[27] ?? null,
                '_11KVSide_LineVoltage_BR' => $row[28] ?? null,
                '_11KVSide_LineCurrent_I1' => $row[29] ?? null,
                '_11KVSide_LineCurrent_I2' => $row[30] ?? null,
                '_11KVSide_LineCurrent_I3' => $row[31] ?? null,
                '_11KVSide_Output_KW' => $row[32] ?? null,
                '_11KVSide_Output_kVAr' => $row[33] ?? null,
                '_11KVSide_PF_Close' => $row[34] ?? null,
                '_11KVSide_Frequency_Hz' => $row[35] ?? null,
                'MainMeterReading_kWH' => $row[36] ?? null,
                'CheckMeterReading_kWH' => $row[37] ?? null,
                'MainMeterUnitIn1hr_kWH' => $row[38] ?? null,
                'CheckMeterUnitIn1hr_kWH' => $row[39] ?? null,
                'Remark' => $row[40] ?? null, // Use null if the column is not present
            ];
        }
// dd($ExcelDatas);
        // Insert all data if there is any
        if (!empty($ExcelDatas)) {
            DB::table('excel_data')->insert($ExcelDatas);
        }

       // For success message
return redirect()->back()->with('success', 'Data imported successfully.');
    } catch (\Exception $e) {
        // For error message
return redirect()->back()->with('error', 'File not supported.');
    }
    }



    public function export_file(){
        $ExcelDatas = ExcelData::all();
        // dd($users);
        $csvData = "id,Date,Time_or_Hour,GenerationUnit1_NozelOpen_%, GenerationUnit1_GeneratorVoltage(KV)_A-Y,GenerationUnit1_GeneratorVoltage(KV)_Y-B, GenerationUnit1_GeneratorVoltage(KV)_B-R, GenerationUnit1_GeneratorCurrent(A)_I1, GenerationUnit1_GeneratorCurrent(A)_I2, GenerationUnit1_GeneratorCurrent(A)_I3, GenerationUnit1_GeneratorOutput_KW, GenerationUnit1_GeneratorOutput_kVAr, GenerationUnit1_PF_Close, GenerationUnit1_Frequency_HZ, GenerationUnit1_Energy_kWH, GenerationUnit2_NozelOpen_%, GenerationUnit2_GeneratorVoltage(KV)_A-Y, GenerationUnit2_GeneratorVoltage(KV)_Y-B, GenerationUnit2_GeneratorVoltage(KV)_B-R, GenerationUnit2_GeneratorCurrent(A)_I1,GenerationUnit2_GeneratorCurrent(A)_I2, GenerationUnit2_GeneratorCurrent(A)_I3, GenerationUnit2_GeneratorOutput_KW,GenerationUnit2_GeneratorOutput_kVAr,GenerationUnit2_PF_Close,GenerationUnit2_Frequency_HZ, GenerationUnit2_Energy_kWH,11KVSide_LineVoltage(KV)_R-Y,11KVSide_LineVoltage(KV)_Y-B,11KVSide_LineVoltage(KV)_B-R, 11KVSide_LineCurrent(A)_I1,11KVSide_LineCurrent(A)_I2,11KVSide_LineCurrent(A)_I3,11KVSide_Output_KW,11KVSide_Output_kVAr,11KVSide_PF_Close,11KVSide_Frequency_Hz,MainMeterReading_kWH,CheckMeterReading_kWH,MainMeterUnitIn1hr_kWH,CheckMeterUnitIn1hr_kWH,Remark\n";

        foreach ($ExcelDatas as $ExcelData) {
            $csvData .= "{$ExcelData->id},{$ExcelData->Date},{$ExcelData->Time_or_Hour},{$ExcelData->GenerationUnit1_NozelOpen},{$ExcelData->GenerationUnit1_GeneratorVoltage_AY},{$ExcelData->GenerationUnit1_GeneratorVoltage_YB},{$ExcelData->GenerationUnit1_GeneratorVoltage_BR},{$ExcelData->GenerationUnit1_GeneratorCurrent_I1},{$ExcelData->GenerationUnit1_GeneratorCurrent_I2},{$ExcelData->GenerationUnit1_GeneratorCurrent_I3},{$ExcelData->GenerationUnit1_GeneratorOutput_KW},{$ExcelData->GenerationUnit1_GeneratorOutput_kVAr}, {$ExcelData->GenerationUnit1_PF_Close},{$ExcelData->GenerationUnit1_Frequency_HZ},{$ExcelData->GenerationUnit1_Energy_kWH}, {$ExcelData->GenerationUnit2_NozelOpen}, {$ExcelData->GenerationUnit2_GeneratorVoltage_AY}, {$ExcelData->GenerationUnit2_GeneratorVoltage_YB}, {$ExcelData->GenerationUnit2_GeneratorVoltage_BR}, {$ExcelData->GenerationUnit2_GeneratorCurrent_I1},{$ExcelData->GenerationUnit2_GeneratorCurrent_I2},{$ExcelData->GenerationUnit2_GeneratorCurrent_I3},{$ExcelData->GenerationUnit2_GeneratorOutput_KW},{$ExcelData->GenerationUnit2_GeneratorOutput_kVAr},{$ExcelData->GenerationUnit2_PF_Close},{$ExcelData->GenerationUnit2_Frequency_HZ},{$ExcelData->GenerationUnit2_Energy_kWH},{$ExcelData->_11KVSide_LineVoltagssss_RY},{$ExcelData->_11KVSide_LineVoltage_YB},{$ExcelData->_11KVSide_LineVoltage_BR},{$ExcelData->_11KVSide_LineCurrent_I1},{$ExcelData->_11KVSide_LineCurrent_I2},{$ExcelData->_11KVSide_LineCurrent_I3},{$ExcelData->_11KVSide_Output_KW},{$ExcelData->_11KVSide_Output_kVAr},{$ExcelData->_11KVSide_PF_Close},{$ExcelData->_11KVSide_Frequency_Hz},{$ExcelData->MainMeterReading_kWH},{$ExcelData->CheckMeterReading_kWH},{$ExcelData->MainMeterUnitIn1hr_kWH},{$ExcelData->CheckMeterUnitIn1hr_kWH},{$ExcelData->Remark}\n";

        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="excel.csv"');
    }
    public function view($id) {
        $data = ExcelData::findOrFail($id);
        return view('admin.view', compact('data'));
    }

    public function edit($id) {
        $data = ExcelData::findOrFail($id);
        return view('admin.edit', compact('data'));
    }

    public function update(Request $request, $id) {
      
        $data = ExcelData::findOrFail($id);
        $data->update($request->all());
        return redirect()->route('admin.import_export')->with('updated', 'Data updated successfully.');
    }

    public function destroy($id) {
        ExcelData::findOrFail($id)->delete();
        return redirect()->route('admin.import_export')->with('deleted', 'Data deleted successfully.');
    }
}
