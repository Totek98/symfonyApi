<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Service\Validate;
use AppBundle\Entity\Post;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryController extends Controller
{

    // GET

    /**
     * @Route("/api/category/get",name="list_category")
     * @Method({"GET"})
     */
    public function listCategory()
    {

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
        'SELECT p.category_name
        FROM AppBundle\Entity\Category p 
        GROUP BY p.category_name');

        $category = $query->getResult();

        if (!count($category)){
            $response=array(

                'code'=>1,
                'message'=>'No category found!',
                'errors'=>null,
                'result'=>null

            );


            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }


        $data=$this->get('jms_serializer')->serialize($category,'json');

        $response=array(

            'code'=>0,
            'message'=>'success',
            'errors'=>null,
            'result'=>json_decode($data)

        );

        return new JsonResponse($response,200);


    }

    // POST 

    /**
     * @param Request $request
     * @param Validate $validate
     * @return JsonResponse
     * @Route("/api/category/add",name="create_category")
     * @Method({"POST"})
     */
    public function createCategory(Request $request,Validate $validate)
    {

        $data=$request->getContent();

        $category=$this->get('jms_serializer')->deserialize($data,'AppBundle\Entity\Category','json');


        $reponse=$validate->validateRequest($category);

        if (!empty($reponse)){
            return new JsonResponse($reponse, Response::HTTP_BAD_REQUEST);
        }

        $category->setDate();
        $em=$this->getDoctrine()->getManager();
        $em->persist($category);

        $em->flush();


        $response=array(

            'code'=>0,
            'message'=>'Category created!',
            'errors'=>null,
            'result'=>null

        );

        return new JsonResponse($response,Response::HTTP_CREATED);
    }

    // DELETE

    /**
     * @Route("/api/category/delete/{id}",name="delete_category")
     * @Method({"DELETE"})
     */

    public function deleteCategory($id)
    {
        $category=$this->getDoctrine()->getRepository('AppBundle:Category')->find($id);

        if (empty($category)) {

            $response=array(

                'code'=>1,
                'message'=>'Category Not Found!',
                'errors'=>null,
                'result'=>null

            );


            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $em=$this->getDoctrine()->getManager();
        $query = $em->createQuery(
        'DELETE FROM AppBundle\Entity\Category c
        WHERE c.id = :id'
        )->setParameter('id', $id);

        $category = $query->execute();
        $em->remove($category);
        $em->flush();

        $response=array(

            'code'=>0,
            'message'=>'Category deleted!',
            'errors'=>null,
            'result'=>null

        );


        return new JsonResponse($response,200);

    }
}