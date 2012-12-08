#include <QDebug>
#include <QApplication>
#include "objectfactory.h"

ObjectFactory* ObjectFactory::instance() { /* static */

     static ObjectFactory* singleton = 0;
     if (singleton == 0) {
         singleton = new ObjectFactory();
         singleton->setParent(qApp); /* guarantees this object is deleted when the application is done */
	}

    return singleton;

}
