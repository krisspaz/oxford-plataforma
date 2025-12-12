<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Str;

// Asegúrate de que tu modelo de usuario esté correctamente importado

class CustomPasswordResetController extends Controller
{
    public function sendResetLink(Request $request)
    {
        // Validar el correo electrónico
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
    
        dd('Correo electrónico validado correctamente.');
    
        // Obtener el usuario
        $user = User::where('email', $request->email)->first();
    
        dd($user);  // Verifica que el usuario se obtiene correctamente
    
        // Generar una nueva contraseña aleatoria
        $newPassword = Str::random(8);
        dd($newPassword);  // Verifica que la nueva contraseña se genera
    
        // Actualizar la contraseña del usuario
        $user->password = bcrypt($newPassword);
        $user->save();
    
        // Enviar la nueva contraseña por SMS
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');
    
        $client = new Client($sid, $token);
    
        try {
            // Enviar el SMS
            $client->messages->create(
                $user->phone_number,
                [
                    'from' => $from,
                    'body' => "Tu nueva contraseña es: {$newPassword}"
                ]
            );
    
            return back()->with('status', 'Te hemos enviado tu nueva contraseña por SMS.');
        } catch (\Exception $e) {
            return back()->withErrors(['sms' => 'Hubo un problema al enviar el SMS: ' . $e->getMessage()]);
        }
    }
}
