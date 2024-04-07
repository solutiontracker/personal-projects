<?php
namespace App\Http\Controllers\Organizer\FormBuilder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\FormBuilderRepository;
class FormBuilderController extends Controller
{        
    protected $repository;
    public function __construct(FormBuilderRepository $formBuilderRepository)
    {
        $this->repository = $formBuilderRepository;
    }

        
    /**
     * createForm
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function createForm(Request $request, $event_id, $registration_form_id = 0)
    {
       $process =  $this->repository->createForm($request->all(), $event_id, $registration_form_id);
       return response()->json($process);
    }
    /**
     * createForm
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function getForms(Request $request, $event_id, $registration_form_id = 0)
    {
       $process =  $this->repository->getForms($request->all(), $event_id, $registration_form_id);
       return response()->json($process);
    }
    
    /**
     * createForm
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function getForm(Request $request, $event_id, $registration_form_id = 0)
    {
       $process =  $this->repository->getForm($request->all(), $event_id, $registration_form_id);
       return response()->json($process);
    }
    
    /**
     * createForm
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function saveSection(Request $request, $event_id, $registration_form_id = 0)
    {
       $process =  $this->repository->saveSection($request->all(), $event_id, $registration_form_id);
       return response()->json($process);
    }
    
    /**
     * createForm
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function saveSectionSort(Request $request, $event_id, $registration_form_id = 0)
    {
       $process =  $this->repository->saveSectionSort($request->all(), $event_id, $registration_form_id);
       return response()->json($process);
    }
    
    /**
     * createForm
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function saveFormSort(Request $request, $event_id, $registration_form_id = 0)
    {
       $process =  $this->repository->saveFormSort($request->all(), $event_id, $registration_form_id);
       return response()->json($process);
    }
    
    /**
     * saveFormStatus
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function saveFormStatus(Request $request, $event_id, $registration_form_id = 0)
    {
       $process =  $this->repository->saveFormStatus($request->all(), $event_id, $registration_form_id);
       return response()->json($process);
    }
    
        
    /**
     * addQuestion
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function addQuestion(Request $request, $event_id, $registration_form_id = 0)
    {
       $process =  $this->repository->addQuestion($request->all(), $event_id, $registration_form_id);
       return response()->json($process);
    }
        
    /**
     * updateQuestion
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function updateQuestion(Request $request, $event_id, $registration_form_id = 0)
    {
       $process =  $this->repository->updateQuestion($request->all(), $event_id, $registration_form_id);
       return response()->json($process);
    }
        
    /**
     * updateQuestionSection
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function updateQuestionSection(Request $request, $event_id, $registration_form_id = 0)
    {
       $process =  $this->repository->updateQuestionSection($request->all(), $event_id, $registration_form_id);
       return response()->json($process);
    }
        
    /**
     * updateQuestionSort
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function updateQuestionSort(Request $request, $event_id, $registration_form_id = 0)
    {
       $process =  $this->repository->updateQuestionSort($request->all(), $event_id, $registration_form_id);
       return response()->json($process);
    }
        
    /**
     * deleteSection
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function deleteSection(Request $request, $event_id, $registration_form_id = 0)
    {
       $process =  $this->repository->deleteSection($request->all(), $event_id, $registration_form_id);
       return response()->json($process);
    }
        
    /**
     * deleteQuestion
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function deleteQuestion(Request $request, $event_id, $registration_form_id = 0)
    {
       $process =  $this->repository->deleteQuestion($request->all(), $event_id, $registration_form_id);
       return response()->json($process);
    }
        
    /**
     * cloneQuestion
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function cloneQuestion(Request $request, $event_id, $registration_form_id = 0)
    {
       $process =  $this->repository->cloneQuestion($request->all(), $event_id, $registration_form_id);
       return response()->json($process);
    }
    
        
    /**
     * cloneSection
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function cloneSection(Request $request, $event_id, $registration_form_id = 0)
    {
       $process =  $this->repository->cloneSection($request->all(), $event_id, $registration_form_id);
       return response()->json($process);
    }
        
    /**
     * submitForm
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function submitForm(Request $request, $event_id, $registration_form_id = 0)
    {
       $process =  $this->repository->submitForm($request->all(), $event_id, $registration_form_id);
       return response()->json($process);
    }
        
    /**
     * getFormAndResult
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function getFormAndResult(Request $request, $event_id, $registration_form_id = 0)
    {
      $data =  $this->repository->getFormAndResult($request->all(), $event_id, $registration_form_id);
       return response()->json($data);
    }
    
    /**
     * renameForm
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function renameForm(Request $request, $event_id, $registration_form_id = 0)
    {
      $data =  $this->repository->renameForm($request->all(), $event_id, $registration_form_id);
       return response()->json($data);
    }
    
    /**
     * copyForm
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function copyForm(Request $request, $event_id, $registration_form_id = 0)
    {
      $data =  $this->repository->copyForm($request->all(), $event_id, $registration_form_id);
       return response()->json($data);
    }
    
    /**
     * deleteForm
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function deleteForm(Request $request, $event_id, $registration_form_id = 0)
    {
      $data =  $this->repository->deleteForm($request->all(), $event_id, $registration_form_id);
       return response()->json($data);
    }
    
    /**
     * saveFormGlobal
     *
     * @param  mixed $request
     * @param  mixed $event_id
     * @param  mixed $registration_form_id
     * @return void
     */
    public function saveFormGlobal(Request $request, $event_id, $registration_form_id = 0)
    {
      $data =  $this->repository->saveFormGlobal($request->all(), $event_id, $registration_form_id);
       return response()->json($data);
    }
    
}
