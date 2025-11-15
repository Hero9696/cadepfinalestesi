<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ChatbotWorkflowController extends Controller
{
    private $chatbotAppPath;

    public function __construct()
    {
        // Define the absolute path to the chatbot's app.js file
        $this->chatbotAppPath = 'C:\\xampp\\htdocs\\miguelCadep\\chatBot-cadep\\bot\\app.js';
    }

    /**
     * Show the form for editing the chatbot workflow file.
     *
     * @return \Inertia\Response
     */
    public function edit()
    {
        try {
            $content = File::get($this->chatbotAppPath);
        } catch (\Exception $e) {
            // Handle case where file doesn't exist or is not readable
            return Redirect::route('dashboard')->with('error', 'No se pudo leer el archivo del chatbot: ' . $e->getMessage());
        }

        return Inertia::render('Chatbot/WorkflowEditor', [
            'fileContent' => $content,
            'filePath' => $this->chatbotAppPath
        ]);
    }

    /**
     * Update the chatbot workflow file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        try {
            // WARNING: This is a dangerous operation.
            // It overwrites the chatbot's main application file.
            File::put($this->chatbotAppPath, $request->input('content'));
        } catch (\Exception $e) {
            return Redirect::route('chatbot.workflow.edit')->with('error', 'Error al guardar el archivo: ' . $e->getMessage());
        }

        return Redirect::route('chatbot.workflow.edit')->with('success', '¡Archivo del chatbot guardado con éxito! Recuerda reiniciar el bot para aplicar los cambios.');
    }
}