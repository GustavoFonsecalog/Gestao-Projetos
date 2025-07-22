<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProjectActionMail;
use Barryvdh\DomPDF\Facade\Pdf;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $projects = Project::with('users')
            ->where('user_id', $user->id)
            ->orWhereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->where('active', true)
            ->get();
        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        // Salvar assinatura se enviada
        if (!empty($data['signature_base64'])) {
            $signature = $data['signature_base64'];
            $signature = preg_replace('/^data:image\/(png|jpg|jpeg);base64,/', '', $signature);
            $signature = str_replace(' ', '+', $signature);
            $signatureName = 'signature_' . uniqid() . '.png';
            \Storage::disk('local')->put('signatures/' . $signatureName, base64_decode($signature));
            $data['signature_path'] = 'signatures/' . $signatureName;
        }
        unset($data['signature_base64']);
        $project = Project::create($data);
        if (isset($data['users'])) {
            $project->users()->sync($data['users']);
        }
        // return response()->json($project->load('users'), 201);
        return redirect('/dashboard')->with('success', 'Projeto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);
        return response()->json($project->load('users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);
        $data = $request->validated();
        $project->update($data);
        if (isset($data['users'])) {
            $project->users()->sync($data['users']);
        }
        return response()->json($project->load('users'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->update(['active' => false]);
        return response()->json(['message' => 'Projeto desativado com sucesso.']);
    }

    public function archive(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return response()->json(['message' => 'Projeto excluído com sucesso.']);
    }

    public function duplicate(Project $project)
    {
        $this->authorize('create', Project::class);
        $newProject = $project->replicate();
        $newProject->title = $project->title . ' (Cópia)';
        $newProject->active = true;
        $newProject->save();
        $newProject->users()->sync($project->users->pluck('id'));
        return response()->json(['message' => 'Projeto duplicado com sucesso.']);
    }

    public function downloadPdf(Project $project)
    {
        $this->authorize('view', $project);
        $project->load('users');
        $pdf = Pdf::loadView('project_pdf', ['project' => $project]);
        return $pdf->download('projeto_'.$project->id.'.pdf');
    }

    public function sendEmail(Request $request, Project $project)
    {
        $this->authorize('view', $project);
        $to = $request->input('to');
        $messageText = $request->input('message', 'Segue informações do projeto.');
        if (!$to) {
            return response()->json(['error' => 'Destinatário não informado.'], 422);
        }
        Mail::to($to)->send(new ProjectActionMail($project, $messageText));
        return response()->json(['message' => 'E-mail enviado com sucesso!']);
    }
}
