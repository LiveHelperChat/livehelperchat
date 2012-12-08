#ifndef OBJECTFACTORY_H
#define OBJECTFACTORY_H

#include <QObject>
#include "abstractfactory.h"

class ObjectFactory : public QObject, public AbstractFactory {
  Q_OBJECT
   public:
     /** @return a singleton instance */
     static ObjectFactory* instance() ;     
    
  protected:
     ObjectFactory() {};
 };

#endif
