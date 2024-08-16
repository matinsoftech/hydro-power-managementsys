@extends('layouts.app')

@section('title', 'Edit Meter Reading')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Edit Meter Reading</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.update', $data->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Date -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Date"><strong>Date:</strong></label>
                            <input type="date" id="Date" name="Date" class="form-control" value="{{ $data->Date }}">
                        </div>
                    </div>
                    <!-- Time -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Time_or_Hour"><strong>Time:</strong></label>
                            <input type="time" id="Time_or_Hour" name="Time_or_Hour" class="form-control" value="{{ $data->Time_or_Hour }}">
                        </div>
                    </div>
                </div>

                <!-- Generation Unit 1 Fields -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit1_NozelOpen"><strong>Generation Unit 1 Nozel Open:</strong></label>
                            <input type="text" id="GenerationUnit1_NozelOpen" name="GenerationUnit1_NozelOpen" class="form-control" value="{{ $data->GenerationUnit1_NozelOpen }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit1_GeneratorVoltage_AY"><strong>Generation unit 1 Generator Voltage (A-Y):</strong></label>
                            <input type="text" id="GenerationUnit1_GeneratorVoltage_AY" name="GenerationUnit1_GeneratorVoltage_AY" class="form-control" value="{{ $data->GenerationUnit1_GeneratorVoltage_AY }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit1_GeneratorVoltage_YB"><strong>Generation unit 1 Generator Voltage (Y-B):</strong></label>
                            <input type="text" id="GenerationUnit1_GeneratorVoltage_YB" name="GenerationUnit1_GeneratorVoltage_YB" class="form-control" value="{{ $data->GenerationUnit1_GeneratorVoltage_YB }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit1_GeneratorVoltage_BR"><strong>Generation unit 1 Generator Voltage (B-R):</strong></label>
                            <input type="text" id="GenerationUnit1_GeneratorVoltage_BR" name="GenerationUnit1_GeneratorVoltage_BR" class="form-control" value="{{ $data->GenerationUnit1_GeneratorVoltage_BR }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit1_GeneratorCurrent_I1"><strong>Generation unit 1 Generator Current (I1):</strong></label>
                            <input type="text" id="GenerationUnit1_GeneratorCurrent_I1" name="GenerationUnit1_GeneratorCurrent_I1" class="form-control" value="{{ $data->GenerationUnit1_GeneratorCurrent_I1 }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit1_GeneratorCurrent_I2"><strong>Generation unit 1 Generator Current (I2):</strong></label>
                            <input type="text" id="GenerationUnit1_GeneratorCurrent_I2" name="GenerationUnit1_GeneratorCurrent_I2" class="form-control" value="{{ $data->GenerationUnit1_GeneratorCurrent_I2 }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit1_GeneratorCurrent_I3"><strong>Generation unit 1 Generator Current (I3):</strong></label>
                            <input type="text" id="GenerationUnit1_GeneratorCurrent_I3" name="GenerationUnit1_GeneratorCurrent_I3" class="form-control" value="{{ $data->GenerationUnit1_GeneratorCurrent_I3 }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit1_GeneratorOutput_KW"><strong>Generation unit 1 Generator Output (KW):</strong></label>
                            <input type="text" id="GenerationUnit1_GeneratorOutput_KW" name="GenerationUnit1_GeneratorOutput_KW" class="form-control" value="{{ $data->GenerationUnit1_GeneratorOutput_KW }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit1_GeneratorOutput_kVAr"><strong>Generation unit 1 Generator Output (kVAr):</strong></label>
                            <input type="text" id="GenerationUnit1_GeneratorOutput_kVAr" name="GenerationUnit1_GeneratorOutput_kVAr" class="form-control" value="{{ $data->GenerationUnit1_GeneratorOutput_kVAr }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit1_PF_Close"><strong>Generation unit 1 PF Close:</strong></label>
                            <input type="text" id="GenerationUnit1_PF_Close" name="GenerationUnit1_PF_Close" class="form-control" value="{{ $data->GenerationUnit1_PF_Close }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit1_Frequency_HZ"><strong>Generation unit 1 Frequency (Hz):</strong></label>
                            <input type="text" id="GenerationUnit1_Frequency_HZ" name="GenerationUnit1_Frequency_HZ" class="form-control" value="{{ $data->GenerationUnit1_Frequency_HZ }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit1_Energy_kWH"><strong>Generation unit 1 Energy (kWH):</strong></label>
                            <input type="text" id="GenerationUnit1_Energy_kWH" name="GenerationUnit1_Energy_kWH" class="form-control" value="{{ $data->GenerationUnit1_Energy_kWH }}">
                        </div>
                    </div>
                </div>

                <!-- Generation Unit 2 Fields -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit2_NozelOpen"><strong>Generation Unit 2 Nozel Open:</strong></label>
                            <input type="text" id="GenerationUnit2_NozelOpen" name="GenerationUnit2_NozelOpen" class="form-control" value="{{ $data->GenerationUnit2_NozelOpen }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit2_GeneratorVoltage_AY"><strong>Generation unit 2 Generator Voltage (A-Y):</strong></label>
                            <input type="text" id="GenerationUnit2_GeneratorVoltage_AY" name="GenerationUnit2_GeneratorVoltage_AY" class="form-control" value="{{ $data->GenerationUnit2_GeneratorVoltage_AY }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit2_GeneratorVoltage_YB"><strong>Generation unit 2 Generator Voltage (Y-B):</strong></label>
                            <input type="text" id="GenerationUnit2_GeneratorVoltage_YB" name="GenerationUnit2_GeneratorVoltage_YB" class="form-control" value="{{ $data->GenerationUnit2_GeneratorVoltage_YB }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit2_GeneratorVoltage_BR"><strong>Generation unit 2 Generator Voltage (B-R):</strong></label>
                            <input type="text" id="GenerationUnit2_GeneratorVoltage_BR" name="GenerationUnit2_GeneratorVoltage_BR" class="form-control" value="{{ $data->GenerationUnit2_GeneratorVoltage_BR }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit2_GeneratorCurrent_I1"><strong>Generation unit 2 Generator Current (I1):</strong></label>
                            <input type="text" id="GenerationUnit2_GeneratorCurrent_I1" name="GenerationUnit2_GeneratorCurrent_I1" class="form-control" value="{{ $data->GenerationUnit2_GeneratorCurrent_I1 }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit2_GeneratorCurrent_I2"><strong>Generation unit 2 Generator Current (I2):</strong></label>
                            <input type="text" id="GenerationUnit2_GeneratorCurrent_I2" name="GenerationUnit2_GeneratorCurrent_I2" class="form-control" value="{{ $data->GenerationUnit2_GeneratorCurrent_I2 }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit2_GeneratorCurrent_I3"><strong>Generation unit 2 Generator Current (I3):</strong></label>
                            <input type="text" id="GenerationUnit2_GeneratorCurrent_I3" name="GenerationUnit2_GeneratorCurrent_I3" class="form-control" value="{{ $data->GenerationUnit2_GeneratorCurrent_I3 }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit2_GeneratorOutput_KW"><strong>Generation unit 2 Generator Output (KW):</strong></label>
                            <input type="text" id="GenerationUnit2_GeneratorOutput_KW" name="GenerationUnit2_GeneratorOutput_KW" class="form-control" value="{{ $data->GenerationUnit2_GeneratorOutput_KW }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit2_GeneratorOutput_kVAr"><strong>Generation unit 2 Generator Output (kVAr):</strong></label>
                            <input type="text" id="GenerationUnit2_GeneratorOutput_kVAr" name="GenerationUnit2_GeneratorOutput_kVAr" class="form-control" value="{{ $data->GenerationUnit2_GeneratorOutput_kVAr }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit2_PF_Close"><strong>Generation unit 2 PF Close:</strong></label>
                            <input type="text" id="GenerationUnit2_PF_Close" name="GenerationUnit2_PF_Close" class="form-control" value="{{ $data->GenerationUnit2_PF_Close }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit2_Frequency_HZ"><strong>Generation unit 2 Frequency (Hz):</strong></label>
                            <input type="text" id="GenerationUnit2_Frequency_HZ" name="GenerationUnit2_Frequency_HZ" class="form-control" value="{{ $data->GenerationUnit2_Frequency_HZ }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GenerationUnit2_Energy_kWH"><strong>Generation unit 2 Energy (kWH):</strong></label>
                            <input type="text" id="GenerationUnit2_Energy_kWH" name="GenerationUnit2_Energy_kWH" class="form-control" value="{{ $data->GenerationUnit2_Energy_kWH }}">
                        </div>
                    </div>
                </div>

                <!-- Switchyard Fields -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Switchyard_33KV_Line1_Voltage"><strong>11KV Side Line Voltage (R-Y):</strong></label>
                            <input type="text" id="_11KVSide_LineVoltage_RY" name="_11KVSide_LineVoltage_RY" class="form-control" value="{{ $data->_11KVSide_LineVoltage_RY }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Switchyard_33KV_Line2_Voltage"><strong>11KV Side Line Voltage (Y-B):</strong></label>
                            <input type="text" id="_11KVSide_LineVoltage_YB" name="_11KVSide_LineVoltage_YB" class="form-control" value="{{ $data->_11KVSide_LineVoltage_YB }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Switchyard_33KV_BusVoltage"><strong>11KV Side Line Voltage (B-R):</strong></label>
                            <input type="text" id="_11KVSide_LineVoltage_BR" name="_11KVSide_LineVoltage_BR" class="form-control" value="{{ $data->_11KVSide_LineVoltage_BR }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Switchyard_Frequency_HZ"><strong>11KV side line Current I1:</strong></label>
                            <input type="text" id="_11KVSide_LineCurrent_I1" name="_11KVSide_LineCurrent_I1" class="form-control" value="{{ $data->_11KVSide_LineCurrent_I1 }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Switchyard_TransformerVoltage_AY"><strong>11KV side line Current I2:</strong></label>
                            <input type="text" id="_11KVSide_LineCurrent_I2" name="_11KVSide_LineCurrent_I2" class="form-control" value="{{ $data->_11KVSide_LineCurrent_I2 }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Switchyard_TransformerVoltage_YB"><strong>11KV side line Current I3:</strong></label>
                            <input type="text" id="_11KVSide_LineCurrent_I3" name="_11KVSide_LineCurrent_I3" class="form-control" value="{{ $data->_11KVSide_LineCurrent_I3 }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Switchyard_TransformerVoltage_BR"><strong>11KV Side Output KW:</strong></label>
                            <input type="text" id="_11KVSide_Output_KW" name="_11KVSide_Output_KW" class="form-control" value="{{ $data->_11KVSide_Output_KW }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Switchyard_TransformerCurrent_I1"><strong>11KV Side Output kVAr:</strong></label>
                            <input type="text" id="_11KVSide_Output_kVAr" name="_11KVSide_Output_kVAr" class="form-control" value="{{ $data->_11KVSide_Output_kVAr }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Switchyard_TransformerCurrent_I2"><strong>11KV Side PF Close:</strong></label>
                            <input type="text" id="_11KVSide_PF_Close" name="_11KVSide_PF_Close" class="form-control" value="{{ $data->_11KVSide_PF_Close }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Switchyard_TransformerCurrent_I3"><strong>11KV Side Frequency Hz:</strong></label>
                            <input type="text" id="_11KVSide_Frequency_Hz" name="_11KVSide_Frequency_Hz" class="form-control" value="{{ $data->_11KVSide_Frequency_Hz }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Switchyard_TransformerCurrent_I3"><strong>Main Meter Reading kWH:</strong></label>
                            <input type="text" id="MainMeterReading_kWH" name="MainMeterReading_kWH" class="form-control" value="{{ $data->MainMeterReading_kWH }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Switchyard_TransformerCurrent_I3"><strong>Check Meter Reading kWH:</strong></label>
                            <input type="text" id="CheckMeterReading_kWH" name="CheckMeterReading_kWH" class="form-control" value="{{ $data->CheckMeterReading_kWH }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Switchyard_TransformerCurrent_I3"><strong>Main Meter Unit In 1hr kWH:</strong></label>
                            <input type="text" id="MainMeterUnitIn1hr_kWH" name="MainMeterUnitIn1hr_kWH" class="form-control" value="{{ $data->MainMeterUnitIn1hr_kWH }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Switchyard_TransformerCurrent_I3"><strong>Check Meter Unit In 1hr kWH:</strong></label>
                            <input type="text" id="CheckMeterUnitIn1hr_kWH" name="CheckMeterUnitIn1hr_kWH" class="form-control" value="{{ $data->CheckMeterUnitIn1hr_kWH }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Switchyard_TransformerCurrent_I3"><strong>Remark:</strong></label>
                            <input type="text" id="Remark" name="Remark" class="form-control" value="{{ $data->Remark }}">
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <input type="submit" value="Save Changes"  class="btn btn-success">
                    <!-- <button type="submit" class="btn btn-success">Save Changes</button> -->
                    <a href="{{ route('admin.import_export') }}" class="btn btn-primary">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
