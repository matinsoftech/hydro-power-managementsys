@extends('layouts.app')

@section('title', 'View Meter Reading')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>View Meter Reading</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Date"><strong>Date:</strong></label>
                        
                        <p id="Date">{{ !empty($data->Date) ? $data->Date : 'No data' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Time_or_Hour"><strong>Time:</strong></label>
                        
                        <p id="Date">{{ !empty($data->Time_or_Hour) ? $data->Time_or_Hour : 'No data' }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
               
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation Unit 1 Nozel Open:</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit1_NozelOpen) ? $data->GenerationUnit1_NozelOpen : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 1 Generator Voltage(A-Y) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit1_GeneratorVoltage_AY	) ? $data->GenerationUnit1_GeneratorVoltage_AY : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 1 Generator Voltage(Y-B) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit1_GeneratorVoltage_YB) ? $data->GenerationUnit1_GeneratorVoltage_YB : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 1 Generator Voltage(B-R) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit1_GeneratorVoltage_BR) ? $data->GenerationUnit1_GeneratorVoltage_BR : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 1 Generator Voltage(I1) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit1_GeneratorCurrent_I1) ? $data->GenerationUnit1_GeneratorCurrent_I1 : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 1 Generator Voltage(I2) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit1_GeneratorCurrent_I2) ? $data->GenerationUnit1_GeneratorCurrent_I2 : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 1 Generator Voltage(I3) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit1_GeneratorCurrent_I3) ? $data->GenerationUnit1_GeneratorCurrent_I3 : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 1 Generator Output(KW) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit1_GeneratorOutput_KW) ? $data->GenerationUnit1_GeneratorOutput_KW : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 1 Generator Output(kVAr) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit1_GeneratorOutput_kVAr) ? $data->GenerationUnit1_GeneratorOutput_kVAr : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 1 PF Close :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit1_PF_Close) ? $data->GenerationUnit1_PF_Close : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 1 Frequency HZ :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit1_Frequency_HZ) ? $data->GenerationUnit1_Frequency_HZ : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 1 Enery KWH :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit1_Energy_kWH) ? $data->GenerationUnit1_Energy_kWH : 'No data' }}</p>


                    </div>
                </div>



                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation Unit 2 Nozel Open:</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit2_NozelOpen) ? $data->GenerationUnit2_NozelOpen : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 2 Generator Voltage(A-Y) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit2_GeneratorVoltage_AY	) ? $data->GenerationUnit2_GeneratorVoltage_AY : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 2 Generator Voltage(Y-B) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit2_GeneratorVoltage_YB) ? $data->GenerationUnit2_GeneratorVoltage_YB : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 2 Generator Voltage(B-R) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit2_GeneratorVoltage_BR) ? $data->GenerationUnit2_GeneratorVoltage_BR : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 2 Generator Voltage(I1) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit2_GeneratorCurrent_I1) ? $data->GenerationUnit2_GeneratorCurrent_I1 : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 2 Generator Voltage(I2) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit2_GeneratorCurrent_I2) ? $data->GenerationUnit2_GeneratorCurrent_I2 : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 2 Generator Voltage(I3) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit2_GeneratorCurrent_I3) ? $data->GenerationUnit2_GeneratorCurrent_I3 : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 2 Generator Output(KW) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit2_GeneratorOutput_KW) ? $data->GenerationUnit2_GeneratorOutput_KW : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 2 Generator Output(kVAr) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit2_GeneratorOutput_kVAr) ? $data->GenerationUnit2_GeneratorOutput_kVAr : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 2 PF Close :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit2_PF_Close) ? $data->GenerationUnit2_PF_Close : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 2 Frequency HZ :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit2_Frequency_HZ) ? $data->GenerationUnit2_Frequency_HZ : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Generation unit 2 Enery KWH :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->GenerationUnit2_Energy_kWH) ? $data->GenerationUnit2_Energy_kWH : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong> 11KV Side line Voltage (RY) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->_11KVSide_LineVoltage_RY) ? $data->_11KVSide_LineVoltage_RY : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong> 11KV Side line Voltage (YB) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->_11KVSide_LineVoltage_YB) ? $data->_11KVSide_LineVoltage_YB : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong> 11KV Side line Voltage (BR) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->_11KVSide_LineVoltage_BR) ? $data->_11KVSide_LineVoltage_BR : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong> 11KV Side line Current (I1) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->_11KVSide_LineCurrent_I1) ? $data->_11KVSide_LineCurrent_I1 : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong> 11KV Side line Current (I2) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->_11KVSide_LineCurrent_I2) ? $data->_11KVSide_LineCurrent_I2 : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong> 11KV Side line Current (I3) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->_11KVSide_LineCurrent_I3) ? $data->_11KVSide_LineCurrent_I3 : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong> 11KV Side line Output(KW) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->_11KVSide_Output_KW) ? $data->_11KVSide_Output_KW : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong> 11KV Side line Output(kVAr) :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->_11KVSide_Output_kVAr) ? $data->_11KVSide_Output_kVAr : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong> 11KV Side PF Close :</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->_11KVSide_PF_Close) ? $data->_11KVSide_PF_Close : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong> 11KV Side Frequency  HZ:</strong></label>
                       <p id="GenerationUnit1_NozelOpen">{{ !empty($data->_11KVSide_Frequency_Hz) ? $data->_11KVSide_Frequency_Hz : 'No data' }}</p>


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="MainMeterReading_kWH"><strong>Main Meter Reading (kWH):</strong></label>
                        <p id="MainMeterReading_kWH">{{ !empty($data->MainMeterReading_kWH)? $data->MainMeterReading_kWH:'No data'}}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Check Meter Reading (kWH):</strong></label>
                       
                        <p id="MainMeterReading_kWH">{{ !empty($data->CheckMeterReading_kWH)? $data->CheckMeterReading_kWH:'No data'}}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Main Meter Unit in 1hr (kWH):</strong></label>
                       
                        <p id="MainMeterReading_kWH">{{ !empty($data->MainMeterUnitIn1hr_kWH)? $data->MainMeterUnitIn1hr_kWH:'No data'}}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Check Meter Unit in 1hr (kWH):</strong></label>
                       
                        <p id="MainMeterReading_kWH">{{ !empty($data->CheckMeterUnitIn1hr_kWH)? $data->CheckMeterUnitIn1hr_kWH:'No data'}}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CheckMeterReading_kWH"><strong>Remark:</strong></label>
                       
                        <p id="MainMeterReading_kWH">{{ !empty($data->Remark)? $data->Remark:'No data'}}</p>
                    </div>
                </div>
            </div>
            <div class="btn-group mt-4" role="group">
                
                <a href="{{ route('admin.import_export') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
