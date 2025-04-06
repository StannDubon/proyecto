<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class doctorController extends Controller
{
    public function index(){
        $doctores = Doctor::all();
        if($doctores->isEmpty()){
            $data=[
                'message' => 'No se encontraron doctores existentes',
                'status' => 200,
            ];
            return response()->json($data, 200);
        }

        $data=[
            'doctores' => $doctores,
            'status' => 200,
        ];
        return response()->json($data, 200);
    }

    public function show($id)
    {
        $doctor = Doctor::find($id);
        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor no encontrado',
                'status' => 404,
            ], 404);
        }
        return response()->json([
            'doctor' => $doctor,
            'status' => 200,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {

        try {
            $validated = $request->validate([
                'nombre' => 'required|max:255',
                'apellido' => 'required|max:255',
                'email' => 'required|email|unique:doctores,email',
                'password' => 'required',
                'clinica_diaria' => 'required|integer',
                'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $image = $request->file('imagen');
            $filename = Str::random(20) . '.' . $image->getClientOriginalExtension(); // Generar nombre único
            $path = $image->storeAs('images', $filename, 'public'); // Guardar la imagen en storage/app/public/images
            $url = Storage::url($path);

            $validated['password'] = Hash::make($validated['password']);
            $validated['imagen'] = $filename;
            $doctor = Doctor::create($validated);

            // No incluyas password ni imagen en la respuesta
            $doctorData = $doctor->toArray();
            unset($doctorData['password']);

            return response()->json([
                'message' => 'Doctor creado exitosamente',
                'doctor' => $doctorData,
                'status' => 201,
            ], 201);

            return response()->json([
                'message' => 'Área creada exitosamente',
                'doctor' => $doctor,
                'status' => 200,
            ], 201);

        } catch (ValidationException $e) {
            $data = [
                'message' => 'Error al validar los datos',
                'errors' => $e->errors(),
                'status' => 422,
            ];
            return response()->json($data, 422);
        }
    }

    public function destroy($id){
        $doctor = Doctor::find($id);
        if(!$doctor){
            return response()->json([
                'message' => 'Doctor no encontrado',
                'status' => 404,
            ], 404);
        }

        $doctor->delete();
        return response()->json([
            'message' => 'Doctor eliminado',
            'status' => 200,
        ], 200);
    }

    public function update(Request $request, $id){
        $doctor = Doctor::find($id);
        if(!$doctor){
            return response()->json([
                'message' => 'Doctor no encontrado',
                'status' => 404,
            ], 404);
        }

        try {
            $validated = $request->validate([
                'nombre' => 'required|max:255',
                'apellido' => 'required|max:255',
                'email' => 'required|email|unique:doctores,email,' . $doctor->id,
                'password' => 'required',
                'clinica_diaria' => 'required|integer'
            ]);

            $doctor->nombre = $request->nombre;
            $doctor->apellido = $request->apellido;
            $doctor->email = $request->email;
            $doctor->password = Hash::make($request->password);
            $doctor->clinica_diaria = $request->clinica_diaria;
            $doctor->save();
            unset($doctor->password);

            return response()->json([
                'message' => 'Doctor actualizado exitosamente',
                'area' => $doctor,
                'status' => 200,
            ], 200);

        } catch (ValidationException $e) {
            $data = [
                'message' => 'Error al validar los datos',
                'errors' => $e->errors(),
                'status' => 422,
            ];
            return response()->json($data, 422);
        }
    }
}
