program LegacyCSV;

{$mode objfpc}{$H+}

uses
  SysUtils, DateUtils, Process, Classes;

function GetEnvDef(const name, def: string): string;
var v: string;
begin
  v := GetEnvironmentVariable(name);
  if v = '' then Exit(def) else Exit(v);
end;

function RandFloat(minV, maxV: Double): Double;
begin
  Result := minV + Random * (maxV - minV);
end;

procedure GenerateXLSX(const csvPath, xlsxPath: string; timestamp: Int64);
var
  xmlContent: TStringList;
  dt: TDateTime;
  dateStr, timeStr: string;
begin
  // Convert Unix timestamp to DateTime
  dt := UnixToDateTime(timestamp);
  dateStr := FormatDateTime('yyyy-mm-dd', dt);
  timeStr := FormatDateTime('hh:nn:ss', dt);

  xmlContent := TStringList.Create;
  try
    // Minimal XLSX structure (Excel XML format for simplicity)
    xmlContent.Add('<?xml version="1.0" encoding="UTF-8"?>');
    xmlContent.Add('<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"');
    xmlContent.Add(' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">');
    xmlContent.Add(' <Worksheet ss:Name="Telemetry">');
    xmlContent.Add('  <Table>');
    xmlContent.Add('   <Row>');
    xmlContent.Add('    <Cell><Data ss:Type="String">Timestamp</Data></Cell>');
    xmlContent.Add('    <Cell><Data ss:Type="String">Date</Data></Cell>');
    xmlContent.Add('    <Cell><Data ss:Type="String">Time</Data></Cell>');
    xmlContent.Add('    <Cell><Data ss:Type="String">Voltage</Data></Cell>');
    xmlContent.Add('    <Cell><Data ss:Type="String">Temperature</Data></Cell>');
    xmlContent.Add('    <Cell><Data ss:Type="String">Active</Data></Cell>');
    xmlContent.Add('    <Cell><Data ss:Type="String">Device</Data></Cell>');
    xmlContent.Add('    <Cell><Data ss:Type="String">Source</Data></Cell>');
    xmlContent.Add('   </Row>');
    
    // Read CSV and convert to XML rows
    var csvLines := TStringList.Create;
    try
      csvLines.LoadFromFile(csvPath);
      if csvLines.Count > 1 then // Skip header
      begin
        var dataLine := csvLines[1];
        var parts: TStringArray;
        parts := dataLine.Split(',');
        
        xmlContent.Add('   <Row>');
        if Length(parts) > 0 then
          xmlContent.Add('    <Cell><Data ss:Type="Number">' + parts[0] + '</Data></Cell>');
        xmlContent.Add('    <Cell><Data ss:Type="String">' + dateStr + '</Data></Cell>');
        xmlContent.Add('    <Cell><Data ss:Type="String">' + timeStr + '</Data></Cell>');
        if Length(parts) > 1 then
          xmlContent.Add('    <Cell><Data ss:Type="Number">' + parts[1] + '</Data></Cell>');
        if Length(parts) > 2 then
          xmlContent.Add('    <Cell><Data ss:Type="Number">' + parts[2] + '</Data></Cell>');
        if Length(parts) > 3 then
          xmlContent.Add('    <Cell><Data ss:Type="String">' + parts[3] + '</Data></Cell>');
        if Length(parts) > 4 then
          xmlContent.Add('    <Cell><Data ss:Type="String">' + parts[4] + '</Data></Cell>');
        if Length(parts) > 5 then
          xmlContent.Add('    <Cell><Data ss:Type="String">' + parts[5] + '</Data></Cell>');
        xmlContent.Add('   </Row>');
      end;
    finally
      csvLines.Free;
    end;
    
    xmlContent.Add('  </Table>');
    xmlContent.Add(' </Worksheet>');
    xmlContent.Add('</Workbook>');
    
    xmlContent.SaveToFile(xlsxPath);
  finally
    xmlContent.Free;
  end;
end;

procedure GenerateAndCopy();
var
  outDir, fn, fullpath, xlsxPath, pghost, pgport, pguser, pgpass, pgdb, copyCmd: string;
  f: TextFile;
  ts: string;
  timestamp: Int64;
  voltage, temp: Double;
  isActive: Boolean;
  deviceName: string;
begin
  outDir := GetEnvDef('CSV_OUT_DIR', '/data/csv');
  ts := FormatDateTime('yyyymmdd_hhnnss', Now);
  fn := 'telemetry_' + ts + '.csv';
  fullpath := IncludeTrailingPathDelimiter(outDir) + fn;
  xlsxPath := IncludeTrailingPathDelimiter(outDir) + 'telemetry_' + ts + '.xlsx';

  // Generate data
  timestamp := DateTimeToUnix(Now);
  voltage := RandFloat(3.2, 12.6);
  temp := RandFloat(-50.0, 80.0);
  isActive := Random(2) = 1;
  deviceName := 'sensor_' + IntToStr(Random(100));

  // Write CSV with proper types
  AssignFile(f, fullpath);
  Rewrite(f);
  Writeln(f, 'timestamp,voltage,temp,is_active,device_name,source_file');
  Writeln(f, IntToStr(timestamp) + ',' +
             FormatFloat('0.00', voltage) + ',' +
             FormatFloat('0.00', temp) + ',' +
             IfThen(isActive, 'ИСТИНА', 'ЛОЖЬ') + ',' +
             '"' + deviceName + '",' +
             '"' + fn + '"');
  CloseFile(f);

  // Generate XLSX with date/time substitution
  WriteLn('Generating XLSX: ', xlsxPath);
  GenerateXLSX(fullpath, xlsxPath, timestamp);

  // COPY into Postgres
  pghost := GetEnvDef('PGHOST', 'db');
  pgport := GetEnvDef('PGPORT', '5432');
  pguser := GetEnvDef('PGUSER', 'monouser');
  pgpass := GetEnvDef('PGPASSWORD', 'monopass');
  pgdb   := GetEnvDef('PGDATABASE', 'monolith');

  // Use psql with COPY FROM PROGRAM for simplicity
  // Here we call psql reading from file
  copyCmd := 'psql "host=' + pghost + ' port=' + pgport + ' user=' + pguser + ' dbname=' + pgdb + '" ' +
             '-c "\copy telemetry_legacy(timestamp, voltage, temp, is_active, device_name, source_file) FROM ''' + fullpath + ''' WITH (FORMAT csv, HEADER true)"';
  // Mask password via env
  SetEnvironmentVariable('PGPASSWORD', pgpass);
  // Execute
  fpSystem(copyCmd);
end;

var period: Integer;
begin
  Randomize;
  period := StrToIntDef(GetEnvDef('GEN_PERIOD_SEC', '300'), 300);
  while True do
  begin
    try
      GenerateAndCopy();
    except
      on E: Exception do
        WriteLn('Legacy error: ', E.Message);
    end;
    Sleep(period * 1000);
  end;
end.
