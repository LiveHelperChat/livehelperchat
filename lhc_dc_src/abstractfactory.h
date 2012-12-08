#ifndef ABSTRACTFACTORY_H
#define ABSTRACTFACTORY_H
 
#include <QString>
#include <QObject>
//#include "dobjs_export.h"
 

class QString;
 /** An Abstract Factory interface 
     @author Alan Ezust
 */
 
//start DOBJS_EXPORT 
class AbstractFactory 
{
   public:
     /**
       @arg className - the desired class to instantiate
       @return a DataObject-derived instance which
        is "close enough" to handle the properties of className.
     */
    virtual ~AbstractFactory() {}
};
//end
 
#endif        //#ifndef ABSTRACTFACTORY_H
