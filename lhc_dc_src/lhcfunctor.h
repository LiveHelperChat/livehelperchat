#ifndef LHCFUNCTOR_H
#define LHCFUNCTOR_H

/**
* @see http://www.newty.de/fpt/functor.html#functors
*
**/

class TFunctor
   {
   public:

      // two possible functions to call member function. virtual cause derived
      // classes will use a pointer to an object and a pointer to a member function
      // to make the function call
      virtual void operator()(QByteArray result)=0;  // call using operator

   };


   // derived template class
   template <class TClass> class TSpecificFunctor : public TFunctor
   {
   private:
      void (TClass::*fpt)(QByteArray);   // pointer to member function
      TClass* pt2Object;                  // pointer to object

   public:

      // constructor - takes pointer to an object and pointer to a member and stores
      // them in two private variables
      TSpecificFunctor(TClass* _pt2Object, void(TClass::*_fpt)(QByteArray))
         { pt2Object = _pt2Object;  fpt=_fpt; };

      // override operator "()"
      virtual void operator()(QByteArray result)
       { (*pt2Object.*fpt)(result);};              // execute member function

   };

#endif
