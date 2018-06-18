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

class PostController extends Controller
{
    // GET

    /**
     * @Route("/api/posts/get",name="list_posts")
     * @Method({"GET"})
     */

    public function listPost()
    {

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
        'SELECT p.id, p.title, p.content
        FROM AppBundle\Entity\Post p');

        $posts = $query->getResult();

        if (!count($posts)){
            $response=array(

                'code'=>1,
                'message'=>'No posts found!',
                'errors'=>null,
                'result'=>null

            );


            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }


        $data=$this->get('jms_serializer')->serialize($posts,'json');

        $response=array(

            'code'=>0,
            'message'=>'success',
            'errors'=>null,
            'result'=>json_decode($data)

        );

        return new JsonResponse($response,200);


    }

    /**
     * @Route("/posts/get",name="list_posts_withoutauth")
     * @Method({"GET"})
     */

    public function listPostwithoutauth()
    {

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
        'SELECT p.title, p.content, p.created, u.username
        FROM AppBundle\Entity\Post p, AppBundle\Entity\User u 
        WHERE p.author = u.id');

        $posts = $query->getResult();

        if (!count($posts)){
            $response=array(

                'code'=>1,
                'message'=>'No posts found!',
                'errors'=>null,
                'result'=>null

            );


            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }


        $data=$this->get('jms_serializer')->serialize($posts,'json');

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
     * @Route("/api/posts/add",name="create_post")
     * @Method({"POST"})
     */
    public function createPost(Request $request,Validate $validate)
    {

        $data=$request->getContent();

        $posts=$this->get('jms_serializer')->deserialize($data,'AppBundle\Entity\Post','json');


        $reponse=$validate->validateRequest($posts);

        if (!empty($reponse)){
            return new JsonResponse($reponse, Response::HTTP_BAD_REQUEST);
        }

        $doctrine = $this->getDoctrine();

        $body=json_decode($request->getContent(), true);

        if($body['kategoria'])
        {
            $category = $doctrine->getRepository('AppBundle:Category')->find($body['kategoria']);
            $posts->setCategory($category);
        }
        else
        {
            $category = $doctrine->getRepository('AppBundle:Category')->find(1);
            $posts->setCategory($category);
        }

        $posts->setUser($this->getUser());
        $em=$this->getDoctrine()->getManager();
        $em->persist($posts);
        $em->flush();

        $response=array(

            'code'=>0,
            'message'=>'Post created!',
            'errors'=>null,
            'result'=>null

        );

        return new JsonResponse($response,Response::HTTP_CREATED);
    }

    // DELETE

    /**
    * @Route("/api/posts/{id}",name="delete_post")
    * @Method({"DELETE"})
    */

    public function deletePost($id)
    {
        $post=$this->getDoctrine()->getRepository('AppBundle:Post')->find($id);

        if (empty($post)) {

            $response=array(

                'code'=>1,
                'message'=>'Post Not Found !',
                'errors'=>null,
                'result'=>null

            );


            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $em=$this->getDoctrine()->getManager();
        $query = $em->createQuery(
        'DELETE FROM AppBundle\Entity\Post p
        WHERE p.id = :id'
        )->setParameter('id', $id);

        $posts = $query->execute();
        $em->remove($post);
        $em->flush();

        $response=array(
            'code'=>0,
            'message'=>'Post deleted !',
            'errors'=>null,
            'result'=>null
        );


        return new JsonResponse($response,200);

    }
}